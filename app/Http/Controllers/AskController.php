<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Storage;

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Announcement;
use Carbon\Carbon;
use App\Models\DailyQuestionCount;
use Illuminate\Support\Facades\DB;

class AskController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        $coins = $user->coins;
        $announcements = $this->getTransformedAnnouncements($user);
        $userTransformed = $this->transformUser($user);
        $limit = $user->getQuestionLimit();
        $remainingQuestions = $limit - $user->getDailyQuestionCount();

        Inertia::share('coins', $coins);

        return Inertia::render('Ask/Index', [
            'coins' => $coins,
            'user' => $userTransformed,
            'announcements' => $announcements,
            'remainingQuestions' => $remainingQuestions,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $now = now();

        $dailyCount = $this->getDailyQuestionCount($user, $now);
        $limit = $user->getQuestionLimit();
        if ($dailyCount['count'] >= $limit) {
            return redirect()->route('ask')->with('error', 'You have reached your daily question limit.');
        }

        DB::beginTransaction();

        try {
            $this->validateRequest($request);

            $file = $request->file('photo');
            $filePath = null;

            if ($file) {
                $mimeType = $file->getMimeType();
                $extension = $file->getClientOriginalExtension();

                $filePath = $this->handleFileUpload($file);

                if (strpos($mimeType, 'image/') === 0) {
                    // No need to process the image, we'll use the file path directly
                } elseif ($mimeType === 'application/pdf') {
                    $filePath = $this->extractTextFromPDF($filePath);
                } elseif (in_array($extension, ['doc', 'docx']) || in_array($mimeType, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
                    $filePath = $this->extractTextFromWord($filePath);
                } else {
                    throw new \Exception('Unsupported file type.');
                }
            }

            $chatGPTResponse = $this->getChatGPTResponse(
                $request,
                $filePath,
                $request->input('instructions'),
                $request->input('subject', 'auto-detect'),
                $request->boolean('steps', false),
                $request->boolean('explain', false),
                $request->input('level')
            );

            if ($chatGPTResponse === null) {
                return redirect()->route('ask')->with('error', 'Nothing Submitted.');
            }

            $processedResponse = $this->processChatGPTResponse($chatGPTResponse, $request);

            $this->createAnnouncement($processedResponse, $filePath, $file ? $file->hashName() : null);

            $this->updateDailyQuestionCount($user, $now, $request->ip(), $dailyCount);

            DB::commit();

            return $this->renderResponse($processedResponse);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in AskController@store: ' . $e->getMessage());
            return redirect()->route('ask')->with('error', $this->getReadableErrorMessage($e));
        }
    }

    private function getReadableErrorMessage(\Exception $e)
    {
        $message = $e->getMessage();

        $readableMessages = [
            'InvalidImageSize' => 'The uploaded image is too large. Please try with a smaller image.',
            'Unsupported file type.' => 'The file type you uploaded is not supported. Please try a different file.',
        ];

        foreach ($readableMessages as $key => $readableMessage) {
            if (strpos($message, $key) !== false) {
                return $readableMessage;
            }
        }

        return 'An error occurred while processing your request. Please try again.';
    }

    private function getTransformedAnnouncements($user)
    {
        return $user->announcements()->orderBy('order')->get()->transform(function ($announcement) {
            return $this->transformAnnouncement($announcement);
        });
    }

    private function getDailyQuestionCount($user, $now)
    {
        $latestCount = $user->dailyQuestionCounts()
            ->whereDate('date', $now->toDateString())
            ->first();

        if (!$latestCount) {
            return ['count' => 0, 'record' => null];
        }

        return ['count' => $latestCount->count, 'record' => $latestCount];
    }

    private function updateDailyQuestionCount($user, $now, $ip, $dailyCount)
    {
        if ($dailyCount['record']) {
            $record = $dailyCount['record'];
            $record->count += 1;
            $ips = explode(',', $record->ip);
            if (!in_array($ip, $ips)) {
                $ips[] = $ip;
                $record->ip = implode(',', $ips);
            }
            $record->save();
        } else {
            $user->dailyQuestionCounts()->create([
                'date' => $now->toDateString(),
                'count' => 1,
                'ip' => $ip,
            ]);
        }
    }

    private function extractTextFromWord($filePath)
    {
        try {
            $fullPath = storage_path('app/' . $filePath);
            $phpWord = IOFactory::load($fullPath);

            $text = '';
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . ' ';
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $text .= $childElement->getText() . ' ';
                            }
                        }
                    }
                }
            }

            \Log::info('Extracted text from Word document:', ['text' => $text]);
            return $text;
        } catch (\Exception $e) {
            \Log::error('Error extracting text from Word document: ' . $e->getMessage());
            return '';
        }
    }

    private function transformAnnouncement($announcement)
    {
        return [
            'title' => $announcement->title,
            'content' => $announcement->content,
            'user_id' => $announcement->user_id,
            'id' => $announcement->id,
            'aiquery' => $announcement->aiquery,
            'subject' => $announcement->subject,
            'extracted_text' => $announcement->extracted_text,
            'instructions' => $announcement->instructions,
            'created_at' => $announcement->created_at,
            'updated_at' => $announcement->updated_at,
            'deleted_at' => $announcement->deleted_at,
            'photo' => $announcement->photo_path ? URL::route('image', ['path' => $announcement->photo_path, 'w' => 400, 'h' => 400, 'fit' => 'crop']) : null,
        ];
    }

    private function transformUser($user)
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'owner' => $user->owner,
            'photo' => $user->photo_path ? URL::route('image', ['path' => $user->photo_path, 'w' => 60, 'h' => 60, 'fit' => 'crop']) : null,
            'deleted_at' => $user->deleted_at,
        ];
    }

    private function validateRequest(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'level' => 'string|nullable|max:255',
            'question' => 'string|nullable',
            'explain' => 'boolean|nullable',
            'steps' => 'boolean|nullable',
            'photo' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx',
            'tokensCost' => 'nullable|integer',
            'temperature' => 'nullable|numeric|min:0|max:1',
            'model' => 'nullable|string',
        ]);
    }

    private function handleFileUpload($file)
    {
        $path = $file->store('uploads', 'public');
        return Storage::disk('public')->path($path);
    }

    private function prepareImageForChatGPT($imagePath)
    {
        $fullPath = storage_path('app/' . $imagePath);
        $imageData = file_get_contents($fullPath);
        return base64_encode($imageData);
    }

    private function extractTextFromPDF($pdfPath)
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile(storage_path('app/' . $pdfPath));
            $extractedText = $pdf->getText();
            \Log::info('Extracted text from PDF:', ['text' => $extractedText]);
            return $extractedText;
        } catch (\Exception $e) {
            \Log::error('Error extracting text from PDF: ' . $e->getMessage());
            return '';
        }
    }

    private function getChatGPTResponse(Request $request, $filePath, $instructions, $subject, $steps, $explain, $level)
    {
        $text = $request->input('question');
        return $this->sendToChatGPT(
            $text,
            $filePath,
            $instructions,
            $subject,
            $steps,
            $explain,
            $level,
            $request->input('tokensCost', 1000),
            $request->input('temperature', 0.6),
            $request->input('model', 'gpt-4o-mini')
        );
    }

    private function sendToChatGPT($text, $filePath, $instructions, $subject, $steps, $explain, $level, $maxTokens, $temperature, $model)
    {
        $apiKey = env('OPENAI_API_KEY');
        $endpoint = 'https://api.openai.com/v1/chat/completions';

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ];

        $systemContent = $this->buildSystemContent($subject, $instructions, $steps, $explain, $level);

        $messages = [
            ['role' => 'system', 'content' => $systemContent],
        ];

        if ($filePath) {
            $base64Image = base64_encode(file_get_contents($filePath));
            $messages[] = [
                'role' => 'user',
                'content' => [
                    ['type' => 'text', 'text' => $text ?: 'Resolve this image'],
                    ['type' => 'image_url', 'image_url' => ['url' => "data:image/jpeg;base64,{$base64Image}"]]
                ]
            ];
        } else {
            $messages[] = ['role' => 'user', 'content' => $text];
        }

        $data = [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => (int) $maxTokens,
            'temperature' => (float) $temperature,
            'top_p' => 1.0,
            'n' => 1,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        $decodedResponse = json_decode($response, true);
        \Log::info($decodedResponse);

        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('Error decoding JSON response from ChatGPT: ' . json_last_error_msg());
            return null;
        }

        return $decodedResponse;
    }

    private function buildSystemContent($subject, $instructions, $steps, $explain, $level)
    {
        $content = "Do not describe the image, extract the text then do the following: You are a homework/test assistant. The current subject is: $subject. If no subject was included, include a string called \"subject=\" followed by one word from this list that best categorizes the content:
        Biology,Chemistry,Computer-Science,Economics,English,Geography,History,Mathematics,Physics,Science. Then, the first sentence should be the exact answer if its a multiple choice, select and respond with the correct answer from the list of answers given, if not solve it and provide the answer. ";

        $content .= "Your response should be structured as follows:\n";
        $content .= "First: Main content (without any specific keyword)\n";

        if ($steps) {
            $content .= "second: Keyword 'steps=' followed by numbered steps explaining key points, ending with '$#$'\n";
        }

        if ($explain) {
            $content .= "Third: Keyword 'explain=' followed by an explanation of the main concepts, ending with '$#$'\n";
        }

        if ($instructions) {
            $content .= $instructions . " ";
        }

        if ($level) {
            $content .= "Ensure all content is presented at a $level level. ";
        }

        return $content;
    }

    private function processChatGPTResponse($chatGPTResponse, Request $request)
    {
        $content = $chatGPTResponse['choices'][0]['message']['content'];
        $createdAt = Carbon::createFromTimestamp($chatGPTResponse['created'] ?? time());

        $subject = $this->extractSubject($content);
        $responseBody = $this->removeSubjectFromResponse($content);

        $stepsText = $this->extractAndRemoveSection($responseBody, 'steps=');
        $explainText = $this->extractAndRemoveSection($responseBody, 'explain=');

        if (empty($stepsText) && empty($explainText)) {
            $mainContent = $responseBody;
        } else {
            $mainContent = trim($responseBody);
        }

        $title = $this->generateTitle($request->input('title'), $createdAt);

        return [
            'responseBody' => $mainContent,
            'stepsText' => $stepsText,
            'explainText' => $explainText,
            'subject' => $subject,
            'title' => $title,
            'createdAt' => $createdAt,
        ];
    }

    private function extractSubject(&$content)
    {
        preg_match('/subject=([^\s]+)/', $content, $matches);
        return isset($matches[1]) ? ucfirst($matches[1]) : null;
    }

    private function removeSubjectFromResponse($content)
    {
        return preg_replace('/\bsubject=[^\s]+(\s|$)/', '', $content);
    }

    private function extractAndRemoveSection(&$content, $keyword)
    {
        $text = "";
        if (preg_match('/' . $keyword . '([^$]*)\s*(\$#\$|$)/', $content, $matches)) {
            $text = trim($matches[1]);
            $content = preg_replace('/' . $keyword . '[^$]*(\$#\$|$)/', '', $content);
        } else {
            $content = preg_replace('/' . $keyword . '[^$]*$/', '', $content);
        }
        return $text;
    }

    private function generateTitle($requestTitle, $createdAt)
    {
        $titleFormatted = $createdAt->format('H:i d M Y');
        return $requestTitle ? "$requestTitle  |  $titleFormatted" : $titleFormatted;
    }

    private function createAnnouncement($processedResponse, $filePath, $originalFileName)
    {
        Auth::user()->announcements()->create([
            'extracted_text' => $filePath ? basename($filePath) : null, // Store only the filename
            'title' => $processedResponse['title'],
            'content' => $processedResponse['responseBody'],
            'aiquery' => request('question'),
            'subject' => $processedResponse['subject'],
            'instructions' => $processedResponse['explainText'] . " " . $processedResponse['stepsText'],
            'photo_path' => $originalFileName,
            'created_at' => $processedResponse['createdAt'],
        ]);
    }

    private function renderResponse($processedResponse)
    {
        $user = Auth::user();
        $userTransformed = $this->transformUser($user);

        $limit = $user->getQuestionLimit();
        $remainingQuestions = $limit - $user->getDailyQuestionCount();

        return Inertia::render('Ask/Index', [
            'coins' => $user->coins,
            'user' => $userTransformed,
            'response' => $processedResponse['responseBody'],
            'stepsResponse' => $processedResponse['stepsText'],
            'explainResponse' => $processedResponse['explainText'],
            'remainingQuestions' => $remainingQuestions,
        ]);
    }
}
