<?php

namespace App\Http\Controllers;
use Aws\Rekognition\RekognitionClient;

use Illuminate\Http\Request;

class FaceCompareController extends Controller
{
   public function compareFromApi(Request $request)
    {
        $request->validate([
            'source_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'target_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            $sourceBytes = file_get_contents($request->file('source_image')->getRealPath());
            $targetBytes = file_get_contents($request->file('target_image')->getRealPath());

            $rekognition = new RekognitionClient([
                'region' => env('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $result = $rekognition->compareFaces([
                'SourceImage' => ['Bytes' => $sourceBytes],
                'TargetImage' => ['Bytes' => $targetBytes],
                'SimilarityThreshold' => 80,
            ]);

            if (!empty($result['FaceMatches'])) {
                return response()->json([
                    'match' => true,
                    'similarity' => $result['FaceMatches'][0]['Similarity'],
                ]);
            } else {
                return response()->json([
                    'match' => false,
                    'message' => 'Wajah tidak cocok.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal membandingkan wajah',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
