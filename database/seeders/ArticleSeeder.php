<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title'       => 'Saat Isi Kepala Menolak Beristirahat: Mengapa Kita Sering "Overthinking" di Malam Hari?',
                'slug'        => 'saat-isi-kepala-menolak-beristirahat-overthinking-malam-hari',
                'category'    => 'Kecemasan',
                'read_time'   => 5,
                'is_featured' => true,
                'reviewer'    => 'Ditinjau Tim Psikolog Remaja',
                'image_path'  => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80',
                'summary'     => 'Ketika dunia luar mulai hening dan lampu kamar dimatikan, alarm tubuh sering kali justru bersuara lebih kencang. Pahami mekanisme respons alami tubuh terhadap rasa lelah mental.',
                'content'     => "Pernahkah kamu merasa sangat mengantuk sepanjang sore, tetapi begitu kepala menyentuh bantal, pikiran justru memutar ulang percakapan tiga hari lalu atau mencemaskan hal yang belum tentu terjadi esok pagi?\n\nFenomena ini sangat wajar. Sepanjang hari, otak kita sibuk merespons tugas, layar ponsel, dan interaksi sosial sehingga tidak sempat memproses emosi yang tertahan. Saat malam tiba dan gangguan dari luar menghilang, otak akhirnya punya ruang kosong—dan sayangnya, ia sering memakainya untuk meninjau ulang hal-hal yang dianggap 'belum selesai'.\n\nTiga langkah lembut yang bisa dicoba malam ini:\n1. Turunkan beban dari kepala ke kertas: Tuliskan maksimal tiga hal yang paling mengganggu pikiranmu di lembar jurnal agar otak merasa hal tersebut sudah 'dicatat' dan aman untuk ditinggalkan sementara.\n2. Ubah fokus ke sensasi fisik: Rasakan berat tubuhmu di atas kasur atau ikuti ritme napas 4-7-8 selama tiga siklus untuk memberi sinyal aman pada sistem saraf.\n3. Berhenti memaksa diri untuk langsung tidur: Jika 20 menit berlalu dan mata masih terjaga, duduklah sejenak di cahaya redup sambil membaca bacaan ringan daripada terus bertarung dengan rasa kesal karena belum tertidur.",
            ],
            [
                'title'       => 'Saat Sekolah Terasa Terlalu Menekan: Mengurai Ekspektasi Sebelum Menjadi Beban',
                'slug'        => 'saat-sekolah-terasa-terlalu-menekan-mengurai-ekspektasi',
                'category'    => 'Stres & Sekolah',
                'read_time'   => 4,
                'is_featured' => false,
                'reviewer'    => 'Konselor Sebaya & Edukator Mental',
                'image_path'  => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=900&q=80',
                'summary'     => 'Ketika deretan tugas dan ujian menumpuk, tubuh kerap memberi sinyal lelah yang tak kasatmata. Cara memilah antara apa yang harus selesai sekarang dan apa yang boleh menunggu esok.',
                'content'     => "Tekanan akademik jarang datang dalam bentuk satu masalah besar; ia biasanya hadir sebagai tumpukan ekspektasi kecil yang datang bersamaan—nilai ujian, organisasi, tugas kelompok, hingga bayangan tentang masa depan.\n\nKetika semua hal terasa mendesak, otak masuk ke mode kewalahan dan justru membuat kita sulit memulai apa pun.\n\nCobalah membagi daftar tugasmu ke dalam tiga keranjang sederhana:\n• Keranjang Hari Ini: Maksimal dua hal kecil yang benar-benar berdampak jika dikerjakan sekarang.\n• Keranjang Esok: Hal-hal yang masih punya tenggat waktu dan tidak akan merusak segalanya jika ditunda malam ini.\n• Keranjang Istirahat: Waktu jeda 15 menit tanpa layar setelah menyelesaikan satu bagian kecil.\n\nIngatlah bahwa nilai dan pencapaian adalah bagian dari perjalananmu, bukan penentu tunggal atas berharganya dirimu sebagai manusia.",
            ],
            [
                'title'       => 'Mengatur Batas dengan Teman Tanpa Harus Merasa Menjadi Orang Jahat',
                'slug'        => 'mengatur-batas-dengan-teman-tanpa-merasa-jahat',
                'category'    => 'Hubungan',
                'read_time'   => 5,
                'is_featured' => false,
                'reviewer'    => 'Edukasi Komunikasi Sehat Remaja',
                'image_path'  => null,
                'summary'     => 'Menolak ajakan saat energimu habis bukanlah tindakan egois. Temukan contoh kalimat yang nyaman dan jujur untuk menyampaikan batasanmu pada sahabat tanpa memutus kehangatan.',
                'content'     => "Banyak dari kita tumbuh dengan keyakinan bahwa teman yang baik adalah teman yang selalu siap sedia kapan pun dibutuhkan. Akibatnya, saat energi sosial kita sedang habis, kita tetap memaksakan diri hadir sambil memendam rasa lelah.\n\nPadahal, batasan (boundaries) bukanlah tembok untuk menjauhkan orang lain, melainkan pagar rendah agar hubungan tetap sehat dan saling menghormati ruang masing-masing.\n\nBeberapa contoh kalimat lembut yang bisa kamu gunakan:\n• \"Aku senang banget diajak, tapi malam ini bateraiku lagi habis banget. Boleh kita jadwalkan ulang akhir pekan ini?\"\n• \"Aku mau dengerin ceritamu dengan utuh, tapi sekarang kepalaku lagi penuh. Boleh aku hubungi kamu lagi sekitar satu jam lagi?\"\n\nTeman yang tulus menyayangimu akan menghargai kejujuranmu daripada memaksamu hadir dalam keadaan kosong.",
            ],
            [
                'title'       => 'Self-Care Bukan Selalu Berbelanja: Merawat Diri dari Hal Paling Sederhana',
                'slug'        => 'self-care-bukan-selalu-berbelanja-merawat-diri-sederhana',
                'category'    => 'Self-care',
                'read_time'   => 3,
                'is_featured' => false,
                'reviewer'    => 'Praktik Kesejahteraan & Mindful Living',
                'image_path'  => null,
                'summary'     => 'Terkadang, merawat diri cukup berarti mematikan ponsel setengah jam lebih awal, meneguk segelas air hangat di beranda, atau memaafkan diri karena belum seproduktif yang direncanakan.',
                'content'     => "Media sosial sering menggambarkan perawatan diri (self-care) sebagai sesuatu yang mahal, estetis, dan penuh persiapan. Ketika kita sedang tidak punya waktu atau biaya untuk itu, kita malah merasa gagal merawat diri.\n\nPada kenyataannya, perawatan diri yang paling memulihkan justru sering kali tidak terlihat mencolok:\n1. Minum segelas air putih penuh sebelum menyentuh media sosial di pagi hari.\n2. Mandi air hangat sambil melemaskan bahu yang seharian menegang di depan layar.\n3. Memberi izin pada diri sendiri untuk tidak membalas pesan non-darurat selama satu jam penuh.\n4. Mengakui dengan jujur bahwa hari ini memang berat, tanpa memaksakan senyum palsu.",
            ],
            [
                'title'       => 'Mengenali Sinyal Fisik Cemas: Saat Jantung Berdegup Cepat dan Dada Terasa Sesak',
                'slug'        => 'mengenali-sinyal-fisik-cemas-jantung-berdegup-cepat',
                'category'    => 'Kecemasan',
                'read_time'   => 4,
                'is_featured' => false,
                'reviewer'    => 'Ditinjau Tim Psikolog & Fisiologi Stres',
                'image_path'  => null,
                'summary'     => 'Rasa cemas sering kali berbicara lewat tubuh terlebih dahulu: napas pendek, telapak tangan dingin, atau getaran halus di lambung. Pelajari cara menenangkan kembali napas.',
                'content'     => "Saat rasa cemas muncul tiba-tiba, tubuh kita sebenarnya sedang berusaha melindungi diri dari sesuatu yang dianggap ancaman. Sistem saraf melepaskan adrenalin yang membuat detak jantung meningkat dan napas menjadi dangkal.\n\nReaksi fisik ini tidak berbahaya, meskipun terasa sangat tidak nyaman. Semakin kita panik memikirkan detak jantung tersebut, semakin kuat tubuh mengirim sinyal bahaya.\n\nCara cepat memutus siklus ini adalah lewat embusan napas yang lebih panjang daripada tarikan napas. Saat kamu menghembuskan napas perlahan selama 6 hingga 8 detik, saraf vagus mengirimkan pesan langsung ke jantung bahwa situasi di sekitarmu aman dan ritme detak bisa kembali melambat.",
            ],
            [
                'title'       => 'Takut Mengecewakan Orang Tua: Membicarakan Rasa Cemas Tanpa Konflik',
                'slug'        => 'takut-mengecewakan-orang-tua-membicarakan-rasa-cemas',
                'category'    => 'Hubungan',
                'read_time'   => 6,
                'is_featured' => false,
                'reviewer'    => 'Spesialis Komunikasi Keluarga & Remaja',
                'image_path'  => null,
                'summary'     => 'Langkah-langkah membuka ruang obrolan hangat dengan keluarga tentang beban ekspektasi yang kamu rasakan, tanpa perlu meninggikan suara atau saling menyalahkan.',
                'content'     => "Banyak anak muda memilih memendam kesulitannya sendirian karena takut terlihat lemah atau menambah beban pikiran orang tua di rumah.\n\nJika kamu ingin mulai bercerita tetapi bingung memulainya, pilihlah waktu ketika suasana rumah sedang santai—bukan di tengah situasi terburu-buru atau saat emosi sedang tinggi.\n\nGunakan kalimat yang berfokus pada perasaanmu sendiri ('I-statement'), bukan menyalahkan:\n\"Bu/Pak, belakangan ini aku lagi merasa cukup kewalahan sama tugas-tugas. Aku nggak minta solusi cepat sekarang, cuma pengin cerita biar lebih lega.\"\n\nMenyampaikan apa yang kamu butuhkan di awal percakapan membantu orang tua memahami bahwa mereka cukup mendengarkan tanpa harus langsung menghakimi atau memberi ceramah panjang.",
            ],
        ];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['slug' => $article['slug']],
                $article
            );
        }
    }
}