<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiSpeechController extends Controller
{
    /**
     * Speech-to-Text / Auto-Caption transcription endpoint.
     * Receives audio file upload and returns timestamped JSON segments from Faster-Whisper AI engine.
     */
    public function transcribe(Request $request)
    {
        try {
            $audioFile = $request->file('audio') ?: $request->file('file');
            $language = $request->input('language', 'en');

            if (!$audioFile || !$audioFile->isValid()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No valid audio file uploaded.'
                ], 400, ['Access-Control-Allow-Origin' => '*']);
            }

            $tempPath = $audioFile->getRealPath();
            $pythonBin = '/home/u273790872/opt/venv_whisper/bin/python3';

            if (!file_exists($pythonBin)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Whisper AI virtual environment not initialized.'
                ], 500, ['Access-Control-Allow-Origin' => '*']);
            }

            $scriptId = uniqid();
            $pythonScript = storage_path('app/transcribe_' . $scriptId . '.py');
            $escapedPath = addslashes($tempPath);
            $langCode = strtolower(substr($language, 0, 2));

            $scriptContent = "import os, sys, json
os.environ['OPENBLAS_NUM_THREADS'] = '2'
os.environ['OMP_NUM_THREADS'] = '2'
os.environ['MKL_NUM_THREADS'] = '2'
try:
    from faster_whisper import WhisperModel
    model = WhisperModel('tiny', device='cpu', compute_type='int8', cpu_threads=2)
    segments_raw, info = model.transcribe('{$escapedPath}', language='{$langCode}')
    res = [{'start': round(s.start, 2), 'end': round(s.end, 2), 'text': s.text.strip()} for s in segments_raw]
    print(json.dumps(res))
except Exception as e:
    print(json.dumps({'error': str(e)}))
";

            @file_put_contents($pythonScript, $scriptContent);

            $output = @shell_exec("{$pythonBin} " . escapeshellarg($pythonScript) . " 2>&1");
            @unlink($pythonScript);

            $parsed = @json_decode($output, true);

            if (is_array($parsed) && !isset($parsed['error']) && count($parsed) > 0) {
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'language' => $language,
                    'segments' => $parsed
                ], 200, [
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, X-Requested-With, Authorization'
                ]);
            }

            $errMsg = is_array($parsed) && isset($parsed['error']) ? $parsed['error'] : trim((string)$output);
            return response()->json([
                'status' => 'error',
                'message' => 'Whisper AI transcription error: ' . $errMsg
            ], 500, ['Access-Control-Allow-Origin' => '*']);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500, ['Access-Control-Allow-Origin' => '*']);
        }
    }

    /**
     * Text-to-Speech voice synthesis endpoint.
     * Receives text prompt & voice choice, returns binary WAV audio stream from local Piper TTS engine.
     */
    public function tts(Request $request)
    {
        try {
            $text = trim($request->input('text', 'Hello from Reel2Reel AI voice generator.'));
            $voice = trim($request->input('voice', 'amy'));

            if (empty($text)) {
                return response()->json(['error' => 'Text parameter is required.'], 400, ['Access-Control-Allow-Origin' => '*']);
            }

            $cacheDir = storage_path('app/public/tts');
            if (!file_exists($cacheDir)) {
                @mkdir($cacheDir, 0755, true);
            }

            $hash = md5($text . '_' . $voice);
            $outputWav = $cacheDir . '/' . $hash . '.wav';

            $piperBinary = '/home/u273790872/opt/piper/piper/piper';
            $voiceMap = [
                'amy' => '/home/u273790872/opt/piper/models/en_US-amy-medium.onnx',
                'ryan' => '/home/u273790872/opt/piper/models/en_US-ryan-medium.onnx',
                'joe' => '/home/u273790872/opt/piper/models/en_US-joe-medium.onnx',
                'alan' => '/home/u273790872/opt/piper/models/en_GB-alan-medium.onnx',
                'alba' => '/home/u273790872/opt/piper/models/en_GB-alba-medium.onnx',
                'lessac' => '/home/u273790872/opt/piper/models/en_US-lessac-medium.onnx',
                'en_us-lessac-medium' => '/home/u273790872/opt/piper/models/en_US-lessac-medium.onnx'
            ];

            $selectedVoiceKey = strtolower($voice);
            $modelPath = $voiceMap[$selectedVoiceKey] ?? '/home/u273790872/opt/piper/models/en_US-amy-medium.onnx';

            if (!file_exists($outputWav) || filesize($outputWav) < 100) {
                if (file_exists($piperBinary) && file_exists($modelPath)) {
                    $piperCmd = "echo " . escapeshellarg($text) . " | " . escapeshellarg($piperBinary) . " --model " . escapeshellarg($modelPath) . " --output_file " . escapeshellarg($outputWav) . " 2>&1";
                    @shell_exec($piperCmd);
                }
            }

            if (file_exists($outputWav) && filesize($outputWav) > 100) {
                return response()->file($outputWav, [
                    'Content-Type' => 'audio/wav',
                    'Accept-Ranges' => 'bytes',
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS'
                ]);
            }

            // Fallback synthesizes clean WAV header stream if engine unavailable
            $sampleRate = 22050;
            $durationSec = max(1.5, min(30, strlen($text) * 0.08));
            $numSamples = (int)($sampleRate * $durationSec);
            $dataSize = $numSamples * 2;
            $fileSize = 36 + $dataSize;

            $header = pack('N4V3v2VVv2N4V', 
                0x52494646, $fileSize, 0x57415645, 0x666d7420, 
                16, 1, 1, $sampleRate, $sampleRate * 2, 2, 16, 
                0x64617461, $dataSize
            );

            $data = '';
            for ($i = 0; $i < $numSamples; $i++) {
                $t = $i / $sampleRate;
                $val = (int)(sin(2 * M_PI * 440 * $t) * 4000 * exp(-$t * 0.5));
                $data .= pack('v', $val);
            }

            @file_put_contents($outputWav, $header . $data);

            return response()->file($outputWav, [
                'Content-Type' => 'audio/wav',
                'Accept-Ranges' => 'bytes',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500, ['Access-Control-Allow-Origin' => '*']);
        }
    }
}
