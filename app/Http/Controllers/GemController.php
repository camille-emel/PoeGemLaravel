<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GemController extends Controller
{
    public function index()
    {
        return view('gems');
    }

    public function fetchData()
    {
        try {
            Log::info('Lecture du fichier JSON local');
            $storageDisk = Storage::disk('public')->exists('gemsData.json');
            if ($storageDisk) {
                Log::info('Fichier gemsData.json trouvé');
                $jsonContent = Storage::disk('public')->get('gemsData.json');
                $data = json_decode($jsonContent, false);
                // Transformation des données pour inclure plus d'informations
                if (isset($data->lines)) {
                    $notCorruptedGems = array_filter($data->lines, function ($line) {
                        return isset($line->corrupted)&&!($line->corrupted);
                    });
                    foreach ($notCorruptedGems as &$gem) {
                        // Ajout d'informations supplémentaires pour l'affichage
                        $gem->displayName = $gem->name;
                        $gem->levelQuality =
                            "Level:$gem->gemLevel/Quality:$gem->gemQuality";
                        // Ajout des variations de prix si disponibles
                        if (isset($gem->sparkline->totalChange)) {
                            $gem->priceChange = number_format($gem->sparkline->totalChange, 2);
                        }
                        // Ajout du nombre de listings si disponible
                        if (isset($gem->listingCount)) {
                            $gem->listings = $gem->listingCount;
                        }
                    }
                }
                return response()->json($data);
            } else {
                Log::info("totoestla");
            }
            Log::error('Fichier gemsData.json non trouvé');
            return response()->json([
                'message' => 'Données non disponibles',
                'error' => 'Fichier gemsData.json non trouvé'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Exception dans fetchData', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
