<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Siswa20262027Seeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $siswa = [
            // -------------------------------------------------------
            // KELAS 2A
            // -------------------------------------------------------
            ['nis' => '3189906290', 'nama' => 'AISHWA GHAITSAA WAHYUDI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3180781414', 'nama' => 'ALMAHIRA SAFALUNA QIRRANNY','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3181803690', 'nama' => 'ALTHAF RHEANDRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3193783050', 'nama' => 'AQMAR IZQIAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3185889135', 'nama' => 'ARSY SOPIATUNNISA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3181448342', 'nama' => 'ATHAFARIZ ADNAN RADEYA FEBRIAWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3186171695', 'nama' => 'AZMIYYA SAHLA KHAIRAATUN HISAAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3188977478', 'nama' => 'EARLYTA NUR ARSYFA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3187247419', 'nama' => 'FILZA SABILLA NURUZZAHRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3180256967', 'nama' => 'HASYA AZURA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3182910176', 'nama' => 'IKLIMA HANA TSABINA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3186691141', 'nama' => 'KEENAN MALISIC HIDAYAT','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3188184248', 'nama' => 'MIKAYLA AZZAHRA SALSABILA PUTRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3181845924', 'nama' => 'MUHAMMAD FAIZ JAZULI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3196234332', 'nama' => 'NAOREEN ALFATHUNISSA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3187894111', 'nama' => 'NAZIHA FATIMAH ZAHRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3184643794', 'nama' => 'NAZKA ISNA HIROPI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3189137819', 'nama' => 'QINARA DYAH PRAMESTHI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3184877198', 'nama' => 'RAFIF RADHIKA ALFARIZKY','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3182370911', 'nama' => 'REFALINA AZKIYA SALSABILA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2A', 'tingkat' => 2],

            // -------------------------------------------------------
            // KELAS 2B
            // -------------------------------------------------------
            ['nis' => '3189360123', 'nama' => 'ABIZAR SHAKEEL SAKHA UTOMO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3183653425', 'nama' => 'ALESHA PUTRI PERMANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3182816569', 'nama' => 'ANINDA KEYRA RAHMA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3184446430', 'nama' => 'ANNISA FITRI RAMADHANI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3198377034', 'nama' => 'ARKA ZAYDAN ALKATIRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3180247218', 'nama' => 'ARSYILA CHELINI ALMAHIRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3181206058', 'nama' => 'AYDAN RUZAIN MELVIANO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3180287986', 'nama' => 'AZRINA ALFATHUNISSA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3188479344', 'nama' => 'DICKY ROYYAN APRILIANSYAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3181859990', 'nama' => 'FAHRIZAL NUR ILHAM','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3189238821', 'nama' => 'FATHAN ARFABIAN AMMAR','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3188370406', 'nama' => 'FURQON NAZRIL ASH SHIDQI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3183231044', 'nama' => 'HISYAM MUHAMMAD GIBRAN ALFARIZY','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3187491179', 'nama' => 'KEYLA AMIRA SALSABILLA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3199092682', 'nama' => 'KHANZA RAPIPAH NURAZMI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3188376300', 'nama' => 'MIKAYLA NUR HABIBAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3197068771', 'nama' => 'MUHAMMAD HAFIZH HABIBI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3186893594', 'nama' => 'NAZIFA FITRA FARZANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3189941465', 'nama' => 'PRADIPTA MAHARDIKA WIDIYATMOKO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3199312373', 'nama' => 'REYNAND ANINDITO ABIMANYU','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2B', 'tingkat' => 2],

            // -------------------------------------------------------
            // KELAS 2C
            // -------------------------------------------------------
            ['nis' => '3184246970', 'nama' => 'ABQORY AULIAN IRWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3192037226', 'nama' => 'ADIBA ZUYYINA SOFIA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3188769062', 'nama' => 'ALIZA ANINDYA SYAKIRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3186014951', 'nama' => 'ARSHAKA EL-RASYID AULIAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3196778276', 'nama' => 'CELLICA NURANGGITA MAHESWARI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3187263818', 'nama' => 'FARABI FAIQ HAMIZAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3186373889', 'nama' => 'FATHIA ALMEERA KHUMAIRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3185412524', 'nama' => 'GAVINDRA AQLAN ALFARIZ','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3189934180', 'nama' => 'GIVANO SHIDQI ALTEZZA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3180214261', 'nama' => 'KEVIN RAFELLO ZYAN KURNIAWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3188891187', 'nama' => 'KHANAYA SYIFA ZUNAIRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3180015315', 'nama' => 'MIKHAYLA SALSABILA PUTRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3184056526', 'nama' => 'MUHAMAD MIRZA PUTRA WIJAYA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3186013344', 'nama' => 'MUHAMAD NUR AFFANDI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3181727989', 'nama' => 'MUHAMMAD ABIMANA MULYA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3187512831', 'nama' => 'MUHAMMAD KHOIRUL AZZAM','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3182956533', 'nama' => 'NALENDRA ADITIA WIBOWO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3193498484', 'nama' => 'QIESHA NUR AZEEZA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3194207901', 'nama' => 'SHEZAN HAFIDZAH BANAFSHA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3197412541', 'nama' => 'ZENI ISLAHHUDIEN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2C', 'tingkat' => 2],

            // -------------------------------------------------------
            // KELAS 2D
            // -------------------------------------------------------
            ['nis' => '3189376861', 'nama' => 'AHMAD NAJIB HUSEIN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3182223793', 'nama' => 'ALESHA SEKAR SHANUM','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3188912022', 'nama' => 'ALSAVA MAULIDA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3184387556', 'nama' => 'AQILA NURNAFISHA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3183118650', 'nama' => 'ARSYLA ATHABINA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3182534512', 'nama' => 'AZKA RAFFASYA ALFARIZQI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3183307313', 'nama' => 'AZKADINA ALMEERA MAIZA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3182868764', 'nama' => 'CHAYRA FAYOLLA AZALIA HERMAWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3191712083', 'nama' => 'DAVIRA MAHARANI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3191584087', 'nama' => 'FIDELA ANINDITA ANATASYA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3184194745', 'nama' => 'GHEA NEYSA KAZUMI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3183862024', 'nama' => 'INTAN SALSABILA SUCIPTO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3192945477', 'nama' => 'JIHAN ALMEIRA NAZAFARIN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3187914035', 'nama' => 'MUHAMMAD ARSHYA PRATAMA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3187533671', 'nama' => 'MUHAMMAD NADHIF ADYATAMA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3186462147', 'nama' => 'MUHAMMAD NUVAIL ALVARROS','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3180453575', 'nama' => 'NAZILLA RIZKY RAMADHANI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3181944439', 'nama' => 'RAFASYA RAFIF AL LATHIF','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3199016167', 'nama' => 'RAHARDYAN SHIBIL PRANA SUSANTO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3190432065', 'nama' => 'TAMA ALBARRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],
            ['nis' => '3189484291', 'nama' => 'ZEA ZAHRA ALFIAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '2D', 'tingkat' => 2],

            // -------------------------------------------------------
            // KELAS 3A
            // -------------------------------------------------------
            ['nis' => '3183874981', 'nama' => 'ADLAN MUHAMMAD KARAMI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3187763594', 'nama' => 'AIMAR FAUZIL ABDILLAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3187621231', 'nama' => 'ALULA FARZANA NAHDA AFIFAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3175751382', 'nama' => 'ARETHA KHANZA ZAYNA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3177522152', 'nama' => 'ASYIFA FAIRUZ ZAYEDA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3178016369', 'nama' => 'DENIS IRAWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3189958612', 'nama' => 'DHAFIN NAUFAL ALRESCHA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3179469233', 'nama' => 'ELSA ALYA AZIZA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3173549440', 'nama' => 'FELISHA AZALEA PUTRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3172905880', 'nama' => 'HISYAM ZAHWAN ALFARUQ','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3178537995', 'nama' => 'IRHAM MALIK ARDIANSYAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3177367764', 'nama' => 'KANIA NAURA AZKAYRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3179239370', 'nama' => 'KHANDRA ALFARIZI AGFIYAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3178137240', 'nama' => 'MUHAMMAD AZZAM SAPUTRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3172773840', 'nama' => 'MUHAMMAD SAKHA ARRAFIF','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3173903923', 'nama' => 'NAUFAL MALIK ALFATIR','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3175891912', 'nama' => 'NAUFAL RIZKY AL FAEYZA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3171848976', 'nama' => 'PUTU ELVANIA GABRIELLA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3173391643', 'nama' => 'RAHIMA ANNISA HILAL','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3172333446', 'nama' => 'RIDWAN ARBAAZ AL HISYAM','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3170662126', 'nama' => 'SABRINA ALMAHYRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3171523397', 'nama' => 'SHAZFA ANNASYA DILLAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3177916571', 'nama' => 'TINARA RACHNA SAHIRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3174862659', 'nama' => 'WIAM YERAN WISTARA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3A', 'tingkat' => 3],

            // -------------------------------------------------------
            // KELAS 3B
            // -------------------------------------------------------
            ['nis' => '3179713547', 'nama' => 'ADZKIA SHEILA ALMIRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3173042561', 'nama' => 'AFRIN RIFFAYA QIRANI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3171145278', 'nama' => 'AISHA ELVINA BILQIS','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3178087954', 'nama' => 'ANNISA LOKASANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3185452029', 'nama' => 'ARFAN CHAIRIL RAFFASYA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3177218927', 'nama' => 'AULIAN FARZAN MUTTAQIN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3179152819', 'nama' => 'CALLISTA AZZAHRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3172495237', 'nama' => 'DAISHA NUR SYAFRINA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3175421362', 'nama' => 'DEO BERLIAN IZDIHAR','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3179694784', 'nama' => 'DHAMAR RAFAN ABRISAM','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3172659037', 'nama' => 'FATIMAH AZZAHRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3177536011', 'nama' => 'HUMAIRA HASNA FATIMAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3160556550', 'nama' => 'JUNA ALFARIZKY','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3179322722', 'nama' => 'KHOERUNNISA HUMAIRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3179056327', 'nama' => 'MUHAMMAD ALBI NUFAIL FAKHRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3177494570', 'nama' => 'MUHAMMAD BAY AL QIANDRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3173824001', 'nama' => 'MUHAMMAD SATRIA ALFARIZQI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3177793321', 'nama' => 'NOVITA SETIANINGSIH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3170984370', 'nama' => 'QAILA ALMAHYRA GUNAWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3173758699', 'nama' => 'RAISYA AMEERA PUTRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3172397387', 'nama' => 'RIHANI NADHIRA PUTRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3177038110', 'nama' => 'SAGITA MARTHA YUNIAR','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3178354930', 'nama' => 'SYAFIQ IBNU ZAIN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3B', 'tingkat' => 3],

            // -------------------------------------------------------
            // KELAS 3C
            // -------------------------------------------------------
            ['nis' => '3187543107', 'nama' => 'ADE YUSUF AL KHAIR','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3175055838', 'nama' => 'ADZKIYA SYAKIRA MECCA DAGRACE','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3185950127', 'nama' => 'ARBI AKSA ATMAJA NARESWARA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3177019291', 'nama' => 'ARSYILA LAUDY KEINARA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3175063909', 'nama' => 'AZARINE ATHIFAH PUTRI ARYANTO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3188995987', 'nama' => 'AZMIA SYAFA AHMAD','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3170845558', 'nama' => 'CINTA SALSABILA SATTOMO PUTRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3171986824', 'nama' => 'DEVENDRA MAHARDIKA NURHADI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3172244053', 'nama' => 'DZAKIA SHEEVA ALMIRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3176267684', 'nama' => 'KEINARRA ARKHANAYA PUTRI GUSTIAMAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3175460183', 'nama' => 'KHANZA ALMIRA ADZKADINA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3173320434', 'nama' => 'KYNA QAILA AZZAHRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3180677615', 'nama' => 'LUTHFI ADAM AL GHIFARI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3179497195', 'nama' => 'MALIK ALTHAFARIZ RIDHWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3188977745', 'nama' => 'MUHAMMAD ARFAN AL RASYID','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3174041881', 'nama' => 'MUHAMMAD IZZAN SYAFIQ','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3178645561', 'nama' => 'NIRWAN AHMAD ABIMANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3176513018', 'nama' => 'NOUREEN MIKAYLA NAYYARA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3179249369', 'nama' => 'RAFFASYA DANISH SISWANTO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3182911310', 'nama' => 'RHAIN HAFIZ ZAIDAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3184743746', 'nama' => 'RYUGA ABIDZAR RAHMAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3175028657', 'nama' => 'SAYYIDA UMMU KHADIJAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3179943832', 'nama' => 'SAYYIDAH HANIFATUN NAJAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3180575460', 'nama' => 'SHEZA NUHA TANZEELA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '3C', 'tingkat' => 3],

            // -------------------------------------------------------
            // KELAS 4A
            // -------------------------------------------------------
            ['nis' => '3172582444', 'nama' => 'ADDARA FREDELLA RIDWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3166358934', 'nama' => 'AHMAD BELVA GHIFARI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3178946859', 'nama' => 'AILSA RIZQY NARESWARI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3176921229', 'nama' => 'AISHA RAIHANAH ZAFIRAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3169292845', 'nama' => 'AKHTARRAYAN MULYA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3164790705', 'nama' => 'ALKHALIFI ZIKRI HADY SUPRIANTO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3167605558', 'nama' => 'AXELA KHALVANY PAMUJI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3163485125', 'nama' => 'BAIM AGATHA SETIAWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3178870377', 'nama' => 'CHERYL KIRANA HANANIA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3173520240', 'nama' => 'DYANDRA KENZO ABINAYA SUKERNO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3168416359', 'nama' => 'ESHAN BUDI PRATAMA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3168699182', 'nama' => 'FATHIYA SHANUM','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3171078386', 'nama' => 'HAFIZH PRADANA PUTRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3161137017', 'nama' => 'KENZIE RAFFASYA ALFAREZKY','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3163864356', 'nama' => 'KHALIQA AZZALEA YHUNOV','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3161881866', 'nama' => 'MIKAILA KHALVANY PAMUJI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3161928900', 'nama' => 'MUHAMMAD ABDURRAHMAN AL FATH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3175569716', 'nama' => 'NAILA KHAERUNIVA IZZATI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3165477096', 'nama' => 'RAFFASYA EL FARESKY ALAWI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3160422994', 'nama' => 'REYNAND ALTHAF RAJENDRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3155461618', 'nama' => 'RIZKY SOPYANDI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3169213505', 'nama' => 'SHIVA MERISYKA PUTRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3160703264', 'nama' => 'UKASYAH ALFATTAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3167107320', 'nama' => 'WYDYA ALIFA NURAULIA MAKU SURO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4A', 'tingkat' => 4],

            // -------------------------------------------------------
            // KELAS 4B
            // -------------------------------------------------------
            ['nis' => '3162407169', 'nama' => 'ABDULLAH ROSYIQUL ABID','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3167032408', 'nama' => 'ABIDAH DHIA SYARAFANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3169154884', 'nama' => 'ADI PUTRA JAYA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3160878456', 'nama' => 'ADIBA ZAHIRA SYAUQI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3178408310', 'nama' => 'ADISTY SARATU JOHAR','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3160999346', 'nama' => 'AISYAH HUMAIRA WAHYUDI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3168550505', 'nama' => 'AISYAH NUHA ZAHIRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3160279452', 'nama' => 'ALESHA ZAHRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3167720543', 'nama' => 'AQILLA RAESHA SALSABILLA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3178360992', 'nama' => 'ARSYA FAIREL ATHARIZZ','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3165294434', 'nama' => 'ATALLA EGAR RAKSYAKA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3173260852', 'nama' => 'AUFA RADEYA VALERIAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3170023688', 'nama' => 'AYUNINDIA GENDHIS YUHASMOKO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3169480039', 'nama' => 'DA\'I TAFTAJANI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3169482300', 'nama' => 'DEHAN ALFARIZKI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3177809818', 'nama' => 'DZAKIRA TALITA ZAHRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3167503526', 'nama' => 'FAIREL ATHARIZ WAHYUDI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3166825942', 'nama' => 'GIBRAN ARVINO FAEYZA KURNIAWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3169613655', 'nama' => 'IBRAHIM GIFFARI ASSAUKI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3168083299', 'nama' => 'KISYA RAHMA ISLAMI YERUSA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3165714126', 'nama' => 'NAILA PUTRI HABBILLAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3162998993', 'nama' => 'NATASYA SULIS SUCIPTO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3168019436', 'nama' => 'RAKKA PUTRA WIJAYA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3169755900', 'nama' => 'SOFIA ZIDNI ALMAHYRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4B', 'tingkat' => 4],

            // -------------------------------------------------------
            // KELAS 4C
            // -------------------------------------------------------
            ['nis' => '3169291617', 'nama' => 'AFSHEEN SYIFA AN NISA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3169032630', 'nama' => 'AIRANI NAURA ARDANI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3171743050', 'nama' => 'ALFARIZKA KARINA PUTRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3166096603', 'nama' => 'ALMEERA ALFATHUNISSA AZZAHRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3162278169', 'nama' => 'ANUGRAH HAKIM','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3160800411', 'nama' => 'ARKA AMAR HIDAYAT','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3176524948', 'nama' => 'ARSAKHA PRADIPTA ABQARI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3167132678', 'nama' => 'ARSYILA NADHIFA AISHA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3168096317', 'nama' => 'AULIYA IZZATUNNISA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3170425878', 'nama' => 'AZZAHRA ADYAH PUTRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3179067090', 'nama' => 'DEFIN VERDIANTO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3170134367', 'nama' => 'ERLYTA ANINDYA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3163725137', 'nama' => 'FATHIA RAHMA FAZIA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3163541793', 'nama' => 'KINANTY ASHEEQA DIEFA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3165514219', 'nama' => 'LUTFI MAHYA HERMAWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3160556962', 'nama' => 'M.FAEYZA TSANY MUBAROK','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3176146856', 'nama' => 'MUHAMMAD ANIS HASANUDIN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3175499789', 'nama' => 'MUHAMMAD AZZAM AL FATIH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3164185575', 'nama' => 'MUSYAARI RASYID SYIHAB','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3169264476', 'nama' => 'RAFASYA SIDDIQ BARUS','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3166855242', 'nama' => 'SYAFIQ HANAN FADLILAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3164430457', 'nama' => 'TARISAH AULA KAHIDA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3169776262', 'nama' => 'ZAIN AHMAD','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '4C', 'tingkat' => 4],

            // -------------------------------------------------------
            // KELAS 5A
            // -------------------------------------------------------
            ['nis' => '3156145636', 'nama' => 'ABQORI ARRAFIF ARSENIO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3153763025', 'nama' => 'ABBAS ZAIN ASH SHIDDIQ','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3154008189', 'nama' => 'ADITYA RAMADHAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3156594249', 'nama' => 'ADNAN FAIZH PRATAMA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '0134260382', 'nama' => 'AKIFA NAILA AHMADI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3156863929', 'nama' => 'ALI ARASY RAMADHAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3167190375', 'nama' => 'ALTHAF DWI PERMANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3165817441', 'nama' => 'AULIA IZZATUNNISA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3166779562', 'nama' => 'AZIZAH SHAKILA ZAHRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3156414983', 'nama' => 'DZAFRANSYAH NURRO PUTRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3150719172', 'nama' => 'FAEYZA THALITA FARZANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3152653283', 'nama' => 'FARZAN ATHALLA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3151973221', 'nama' => 'HAFIDZ NUMAN RIFAI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3153090217', 'nama' => 'KIANU LINTANG PERMANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3157549444', 'nama' => 'KIRANA FILIA NATHASYIFANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3167973283', 'nama' => 'MUHAMMAD FARREL AR RASYID','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3154889911', 'nama' => 'MUTHIA LATHIFA ZAHRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3156843151', 'nama' => 'NAWA HIDNA KIRANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3155313540', 'nama' => 'RAIIFHA NADHINE SHIDQIYYA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3150284605', 'nama' => 'SEKAR AZIZAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3153863620', 'nama' => 'YASMIN NUR ASSYIFA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5A', 'tingkat' => 5],

            // -------------------------------------------------------
            // KELAS 5B
            // -------------------------------------------------------
            ['nis' => '0161377016', 'nama' => 'ADEEVA AYUDIA INARA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3159023707', 'nama' => 'ANILA HASNA SOFYANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3161506136', 'nama' => 'ARDENI NABIL PRADIPTA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3157525980', 'nama' => 'ARINA MIKAILA SAKHI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3156930921', 'nama' => 'AZKANIA SASHI KIRANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3155930176', 'nama' => 'DZAKIYYA MUNA AZIZAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3150186157', 'nama' => 'FARID SIDIK PERMANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3156799004', 'nama' => 'GALANG ADIPUTRA SURYATAMA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3167387494', 'nama' => 'GENDIS MAHESWARI WIBOWO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3150789584', 'nama' => 'HANIFAH FITRI ARMIZA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3153606938', 'nama' => 'KHAIRA PITALOKA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3158794638', 'nama' => 'MOCHAMMAD AZKA ADHITYA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3153070079', 'nama' => 'MUHAMMAD FATHUL ISLAM','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3152281684', 'nama' => 'NAILA HERMANSYAH PUTRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3163372020', 'nama' => 'NAJMA ORLIN RAMADHANTY','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3150662932', 'nama' => 'NAVARA ADZKIYYAH ADZRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3165609049', 'nama' => 'RE INARA AYAKA MAULIDIA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3169866087', 'nama' => 'UKHTIA RAHMA DESWANTI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],
            ['nis' => '3155513854', 'nama' => 'WILDAN FIRDAUS','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5B', 'tingkat' => 5],

            // -------------------------------------------------------
            // KELAS 5C
            // -------------------------------------------------------
            ['nis' => '3156281351', 'nama' => 'ABIYASA RASKIA SANTANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3153497805', 'nama' => 'ADAM AIRDAN HABIBIE','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3159158445', 'nama' => 'ADIBA SALSABILA FATHIYATUROKHMA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3155591517', 'nama' => 'AFIFAH NADHIF ATIKAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3163668943', 'nama' => 'AHMAD FAEYZA DAFIQ IBRAHIM','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '0155560225', 'nama' => 'ALESHA NAURA CHANTIKA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3151995000', 'nama' => 'ANTONY TRISTAN AL JARAS','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3151473373', 'nama' => 'ASYIFA AULIA AZZAHRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3162007804', 'nama' => 'AZALEA KHALIQA DZAHIN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3165133844', 'nama' => 'DZAKIYA NUR AZIZAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3150719173', 'nama' => 'FAEYZA ARKANANTA PRANAJA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3162795283', 'nama' => 'FALIH NOER UBAIDILLAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3159527690', 'nama' => 'FIKRI AHMAD HAMDHANI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3158518036', 'nama' => 'HAURA NUR AKILA ASMORO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '0133752015', 'nama' => 'LARISA WAHYU HADZIQAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3158621148', 'nama' => 'MUHAMMAD EL QIANO RAMADAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3152820989', 'nama' => 'NAJMA NUR SAFFANAH FEBRIAWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '3152427578', 'nama' => 'SALMA SALSABILA FIRDAUS','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],
            ['nis' => '0153017073', 'nama' => 'ZIAN AULIA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '5C', 'tingkat' => 5],

            // -------------------------------------------------------
            // KELAS 6A
            // -------------------------------------------------------
            ['nis' => '3140562603', 'nama' => 'AIKO ARTA PRADANA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3144071892', 'nama' => 'AJENG PRAMESWARI HAIDAH','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3142165247', 'nama' => 'ANNISA DIANDRA RAMADHANI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3140463177', 'nama' => 'ASLAN RAMADENTA PUTRA ARYANTO','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3158820686', 'nama' => 'AZARINE YUSRA FAHIMA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0159812639', 'nama' => 'GALIH SETIAGUNG PUTRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3143314872', 'nama' => 'GIBRAN ARTANABIL','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0143704181', 'nama' => 'HANAFI AL ZAFRAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3148017887', 'nama' => 'IFFA ASTILA RAHMA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],

            // -------------------------------------------------------
            // KELAS 6A
            // -------------------------------------------------------
            ['nis' => '0153871451', 'nama' => 'KIKANDRIA AULIA PUTRI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0151850782', 'nama' => 'MUH BAGUS SATTOMO PUTRA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0156860680', 'nama' => 'MUHAMMAD DIRGHAM AL-AQSA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0148585809', 'nama' => 'NICI FAAZA QIRANI','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0149232639', 'nama' => 'OZIL ANDRA HERMAWAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3140979203', 'nama' => 'REIHAN EKA RAMADHAN','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3140972787', 'nama' => 'UWAIS ALFARUQ','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3146902710', 'nama' => 'WIDYA ARIFIANA SALSABILLA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0145374977', 'nama' => 'WINDRIYA ANINDITA','jenis_sekolah' => 'SD', 'tahun_ajaran' => '2026-2027', 'kelas' => '6A', 'tingkat' => 6],

        ];

        $rows = array_map(function ($s) use ($now) {
            $tingkat = $s['tingkat'];

            if ($tingkat >= 1 && $tingkat <= 6) {
                $jenisSekolah = 'SD';
            } elseif ($tingkat >= 7 && $tingkat <= 9) {
                $jenisSekolah = 'SMP';
            } else {
                $jenisSekolah = null;
            }

            return [
                'nis'             => $s['nis'],
                'nama'            => $s['nama'],
                'kelas'           => $s['kelas'],
                'jenis_sekolah'   => $jenisSekolah,
                'tingkat'         => $tingkat,
                'tahun_ajaran'    => '2026/2027',
                'nama_orang_tua'  => null,
                'no_hp_orang_tua' => null,
                'email_orang_tua' => null,
                'is_calon'        => false,
                'calon_jenis'     => null,
                'status_aktif'    => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }, $siswa);

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('siswa')->insert($chunk);
        }
    }
}
