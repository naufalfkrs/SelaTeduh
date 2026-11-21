<?php

namespace App\Support;

/**
 * Palet & ikon untuk setiap pilihan mood, mengikuti desain Figma SelaTeduh.
 */
class Mood
{
    public const OPTIONS = [
        'Tenang' => [
            'icon'  => 'cloud',
            'dot'   => 'bg-primary',
            'solid' => 'bg-primary text-white',
            'soft'  => 'bg-secondary text-primary',
        ],
        'Biasa Saja' => [
            'icon'  => 'sun',
            'dot'   => 'bg-[#8FA394]',
            'solid' => 'bg-[#8FA394] text-white',
            'soft'  => 'bg-[#EEF1EC] text-[#4F6153]',
        ],
        'Lelah' => [
            'icon'  => 'moon',
            'dot'   => 'bg-[#B98B6B]',
            'solid' => 'bg-[#8A5A3C] text-white',
            'soft'  => 'bg-tertiary-soft text-tertiary-dark',
        ],
        'Resah' => [
            'icon'  => 'zap',
            'dot'   => 'bg-tertiary',
            'solid' => 'bg-tertiary text-[#5A3520]',
            'soft'  => 'bg-[#FBEFE7] text-[#9A5B36]',
        ],
        'Kewalahan' => [
            'icon'  => 'waves',
            'dot'   => 'bg-[#3E3A36]',
            'solid' => 'bg-[#3E3A36] text-white',
            'soft'  => 'bg-[#ECEAE7] text-[#3E3A36]',
        ],
    ];

    public const TRIGGERS = [
        'Tugas/Sekolah',
        'Tidur Kurang',
        'Hubungan Teman',
        'Keluarga',
        'Waktu Sendiri',
        'Kabar Berita/Medsos',
    ];

    public static function labels(): array
    {
        return array_keys(self::OPTIONS);
    }

    public static function get(?string $mood, string $key, string $fallback = ''): string
    {
        return self::OPTIONS[$mood][$key] ?? $fallback;
    }
}
