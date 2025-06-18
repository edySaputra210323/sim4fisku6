<?php

namespace App\Helpers;

class InspiringID
{
    public static function quote()
    {
        // Define arrays for different parts of the quote.
        $introPhrases = [
            "Percayalah bahwa",
            "Ingatlah bahwa",
            "Selalu ingat bahwa",
            "Jangan lupa bahwa",
            "Ketahuilah bahwa",
            "Setiap hari adalah kesempatan untuk"
        ];

        $motivationalPhrases = [
            "setiap tantangan adalah peluang untuk tumbuh.",
            "impianmu menunggu untuk diwujudkan.",
            "keberhasilan adalah hasil dari kerja keras.",
            "kegagalan hanyalah batu loncatan menuju sukses.",
            "kamu memiliki potensi yang luar biasa.",
            "semua mimpi dapat tercapai dengan usaha dan doa.",
            "keberanian membuka pintu masa depan.",
            "kesuksesan dimulai dari langkah kecil hari ini.",
            "perubahan besar berasal dari tindakan sederhana."
        ];

        // Optionally, cache the generated quotes to avoid re-generating them on every call.
        static $quotes = null;
        if ($quotes === null) {
            $quotes = [];
            for ($i = 0; $i < 1000; $i++) {
                $quotes[] = $introPhrases[array_rand($introPhrases)] . " " . $motivationalPhrases[array_rand($motivationalPhrases)];
            }
        }

        // Return a random quote from the generated list.
        return $quotes[array_rand($quotes)];
    }
}
