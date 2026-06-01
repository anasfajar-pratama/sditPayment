<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Siswa2026Seeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $siswa = [
            // -------------------------------------------------------
            // KELAS 1A
            // -------------------------------------------------------
            ['nis' => null,           'nama' => 'AISHWA GHAITSAA WAHYUDI',              'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ALMAHIRA SAFALUNA QIRRANNY',            'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ALTHAF RHEANDRA',                       'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'AQMAR IZQIAN',                          'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ARSY SOPIATUNNISA',                     'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ATHAFARIZ ADNAN RADEYA FEBRIAWAN',      'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'AZMIYYA SAHLA KHAIRAATUN HISAAN',       'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'EARLYTA NUR ARSYFA',                    'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'FILZA SABILLA NURUZZAHRA',              'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'HASYA AZURA',                           'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'IKLIMA HANA TSABINA',                   'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'KEENAN MALISIC HIDAYAT',                'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'MIKAYLA AZZAHRA SALSABILA PUTRI',       'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'MUHAMMAD FAIZ JAZULI',                  'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'NAOREEN ALFATHUNISSA',                  'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'NAZIHA FATIMAH ZAHRA',                  'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'NAZKA ISNA HIROPI',                     'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'QINARA DYAH PRAMESTHI',                 'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'REFALINA AZKIYA SALSABILA',             'kelas' => '1A', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'RAFIF RADHIKA ALFARIZKY',               'kelas' => '1A', 'tingkat' => 1],

            // -------------------------------------------------------
            // KELAS 1B
            // -------------------------------------------------------
            ['nis' => null,           'nama' => 'ABIZAR SHAKEEL SAKHA UTOMO',            'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ALESHA PUTRI PERMANA',                  'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ANINDA KEYRA RAHMA',                    'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ANNISA FITRI RAMADHANI',                'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ARKA ZAYDAN ALKATIRI',                  'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ARSYILA CHELINI ALMAHIRA',              'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'AYDAN RUZAIN MELVIANO',                 'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'AZRINA ALFATHUNISSA',                   'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'DICKY ROYYAN APRILIANSYAH',             'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'FAHRIZAL NUR ILHAM',                    'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'FATHAN ARFABIAN AMMAR',                 'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'FURQON NAZRIL ASH SHIDQI',              'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'HISYAM MUHAMMAD GIBRAN ALFARIZY',       'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'KEYLA AMIRA SALSABILLA',                'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'KHANZA RAPIPAH NURAZMI',                'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'MIKAYLA NUR HABIBAH',                   'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'MUHAMMAD HAFIZH HABIBI',                'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'NAZIFA FITRA FARZANA',                  'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'PRADIPTA MAHARDIKA WIDIYATMOKO',        'kelas' => '1B', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'REYNAND ANINDITO ABIMANYU',             'kelas' => '1B', 'tingkat' => 1],

            // -------------------------------------------------------
            // KELAS 1C
            // -------------------------------------------------------
            ['nis' => null,           'nama' => 'ABQORY AULIAN IRWAN',                   'kelas' => '1C', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ADIBA ZUYYINA SOFIA',                   'kelas' => '1C', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ALIZA ANINDYA SYAKIRA',                 'kelas' => '1C', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ARSHAKA EL-RASYID AULIAN',              'kelas' => '1C', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'CELLICA NURANGGITA MAHESWARI',          'kelas' => '1C', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'FARABI FAIQ HAMIZAN',                   'kelas' => '1C', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'FATHIA ALMEERA KHUMAIRA',               'kelas' => '1C', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'GAVINDRA AQLAN ALFARIZ',                'kelas' => '1C', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'GIVANO SHIDQI ALTEZZA',                 'kelas' => '1C', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'KEVIN RAFELLO ZYAN KURNIAWAN',          'kelas' => '1C', 'tingkat' => 1],

            // -------------------------------------------------------
            // KELAS 1D
            // -------------------------------------------------------
            ['nis' => null,           'nama' => 'AHMAD NAJIB HUSEIN',                    'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ALESHA SEKAR SHANUM',                   'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ALSAVA MAULIDA',                        'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'AQILA NURNAFISHA',                      'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ARSYLA ATHABINA',                       'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'AZKA RAFFASYA ALFARIZQI',               'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'AZKADINA ALMEERA MAIZA',                'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'CHAYRA FAYOLLA AZALIA HERMAWAN',        'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'DAVIRA MAHARANI',                       'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'FIDELA ANINDITA ANATASYA',              'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'GHEA NEYSA KAZUMI',                     'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'INTAN SALSABILA SUCIPTO',               'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'JIHAN ALMEIRA NAZAFARIN',               'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'MUHAMMAD ARSHYA PRATAMA',               'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'MUHAMMAD NADHIF ADYATAMA',              'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'MUHAMMAD NUVAIL ALVARROS',              'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'NAZILLA RIZKY RAMADHANI',               'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'RAFASYA RAFIF AL LATHIF',               'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'RAHARDYAN SHIBIL PRANA SUSANTO',        'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'TAMA ALBARRA',                          'kelas' => '1D', 'tingkat' => 1],
            ['nis' => null,           'nama' => 'ZEA ZAHRA ALFIAN',                      'kelas' => '1D', 'tingkat' => 1],

            // -------------------------------------------------------
            // KELAS 2A
            // -------------------------------------------------------
            ['nis' => '3183874981',   'nama' => 'ADLAN MUHAMMAD KARAMI',                 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3187763594',   'nama' => 'AIMAR FAUZIL ABDILLAH',                 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3187621231',   'nama' => 'ALULA FARZANA NAHDA AFIFAH',            'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3175751382',   'nama' => 'ARETHA KHANZA ZAYNA',                   'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3177522152',   'nama' => 'ASYIFA FAIRUZ ZAYEDA',                  'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3178016369',   'nama' => 'DENIS IRAWAN',                          'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3189958612',   'nama' => 'DHAFIN NAUFAL ALRESCHA',                'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3179469233',   'nama' => 'ELSA ALYA AZIZA',                       'kelas' => '2A', 'tingkat' => 2],
            ['nis' => null,           'nama' => 'FELISHA AZALEA PUTRI',                  'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3172905880',   'nama' => 'HISYAM ZAHWAN ALFARUQ',                 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3178537995',   'nama' => 'IRHAM MALIK ARDIANSYAH',                'kelas' => '2A', 'tingkat' => 2],
            ['nis' => null,           'nama' => 'KANIA NAURA AZKAYRA',                   'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3179239370',   'nama' => 'KHANDRA ALFARIZI AGFIYAN',              'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3178137240',   'nama' => 'MUHAMMAD AZZAM SAPUTRA',                'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3172773840',   'nama' => 'MUHAMMAD SAKHA ARRAFIF',                'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3173903923',   'nama' => 'NAUFAL MALIK ALFATIR',                  'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3175891912',   'nama' => 'NAUFAL RIZKY AL FAEYZA',                'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3171848976',   'nama' => 'PUTU ELVANIA GABRIELLA',                'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3173391643',   'nama' => 'RAHIMA ANNISA HILAL',                   'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3172333446',   'nama' => 'RIDWAN ARBAAZ AL HISYAM',               'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3170662126',   'nama' => 'SABRINA ALMAHYRA',                      'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3171523397',   'nama' => 'SHAZFA ANNASYA DILLAH',                 'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3177916571',   'nama' => 'TINARA RACHNA SAHIRA',                  'kelas' => '2A', 'tingkat' => 2],
            ['nis' => '3174862659',   'nama' => 'WIAM YERAN WISTARA',                    'kelas' => '2A', 'tingkat' => 2],

            // -------------------------------------------------------
            // KELAS 2B
            // -------------------------------------------------------
            ['nis' => '3179713547',   'nama' => 'ADZKIA SHEILA ALMIRA',                  'kelas' => '2B', 'tingkat' => 2],
            ['nis' => null,           'nama' => 'AFRIN RIFFAYA QIRANI',                  'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3171145278',   'nama' => 'AISHA ELVINA BILQIS',                   'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3178087954',   'nama' => 'ANNISA LOKASANA',                       'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3185452029',   'nama' => 'ARFAN CHAIRIL RAFFASYA',                'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3177218927',   'nama' => 'AULIAN FARZAN MUTTAQIN',                'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3179152819',   'nama' => 'CALLISTA AZZAHRA',                      'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3172495237',   'nama' => 'DAISHA NUR SYAFRINA',                   'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3175421362',   'nama' => 'DEO BERLIAN IZDIHAR',                   'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3179694784',   'nama' => 'DHAMAR RAFAN ABRISAM',                  'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3172659037',   'nama' => 'FATIMAH AZZAHRA',                       'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3177536011',   'nama' => 'HUMAIRA HASNA FATIMAH',                 'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3160556550',   'nama' => 'JUNA ALFARIZKY',                        'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3179322722',   'nama' => 'KHOERUNNISA HUMAIRA',                   'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3179056327',   'nama' => 'MUHAMMAD ALBI NUFAIL FAKHRI',           'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3177494570',   'nama' => 'MUHAMMAD BAY AL QIANDRA',               'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3173824001',   'nama' => 'MUHAMMAD SATRIA ALFARIZQI',             'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3178711326',   'nama' => 'NAUFAL KURNIAWAN AZHARY',               'kelas' => '2B', 'tingkat' => 2],
            ['nis' => null,           'nama' => 'NOVITA SETIANINGSIH',                   'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3170984370',   'nama' => 'QAILA ALMAHYRA GUNAWAN',                'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3173758699',   'nama' => 'RAISYA AMEERA PUTRI',                   'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3172397387',   'nama' => 'RIHANI NADHIRA PUTRI',                  'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3177038110',   'nama' => 'SAGITA MARTHA YUNIAR',                  'kelas' => '2B', 'tingkat' => 2],
            ['nis' => '3178354930',   'nama' => 'SYAFIQ IBNU ZAIN',                      'kelas' => '2B', 'tingkat' => 2],

            // -------------------------------------------------------
            // KELAS 2C
            // -------------------------------------------------------
            ['nis' => '3187543107',   'nama' => 'ADE YUSUF AL KHAIR',                    'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3175055838',   'nama' => 'ADZKIYA SYAKIRA MECCA DAGRACE',         'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3185950127',   'nama' => 'ARBI AKSA ATMAJA NARESWARA',            'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3177019291',   'nama' => 'ARSYILA LAUDY KEINARA',                 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3175063909',   'nama' => 'AZARINE ATHIFAH PUTRI ARYANTO',         'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3188995987',   'nama' => 'AZMIA SYAFA AHMAD',                     'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3170845558',   'nama' => 'CINTA SALSABILA SATTOMO PUTRI',         'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3171986824',   'nama' => 'DEVENDRA MAHARDIKA NURHADI',            'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3172244053',   'nama' => 'DZAKIA SHEEVA ALMIRA',                  'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3176267684',   'nama' => 'KEINARRA ARKHANAYA PUTRI GUSTIAMAN',   'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3175460183',   'nama' => 'KHANZA ALMIRA ADZKADINA',               'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3173320434',   'nama' => 'KYNA QAILA AZZAHRA',                    'kelas' => '2C', 'tingkat' => 2],
            ['nis' => null,           'nama' => 'LUTHFI ADAM AL GHIFARI',                'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3179497195',   'nama' => 'MALIK ALTHAFARIZ RIDHWAN',              'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3188977745',   'nama' => 'MUHAMMAD ARFAN AL RASYID',              'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3174041881',   'nama' => 'MUHAMMAD IZZAN SYAFIQ',                 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3178645561',   'nama' => 'NIRWAN AHMAD ABIMANA',                  'kelas' => '2C', 'tingkat' => 2],
            ['nis' => null,           'nama' => 'NOUREEN MIKAYLA NAYYARA',               'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3179249369',   'nama' => 'RAFFASYA DANISH SISWANTO',              'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3182911310',   'nama' => 'RHAIN HAFIZ ZAIDAN',                    'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3184743746',   'nama' => 'RYUGA ABIDZAR RAHMAN',                  'kelas' => '2C', 'tingkat' => 2],
            ['nis' => null,           'nama' => 'SAYYIDA UMMU KHADIJAH',                 'kelas' => '2C', 'tingkat' => 2],
            ['nis' => '3179943832',   'nama' => 'SAYYIDAH HANIFATUN NAJAH',              'kelas' => '2C', 'tingkat' => 2],
            ['nis' => null,           'nama' => 'SHEZA NUHA TANZEELA',                   'kelas' => '2C', 'tingkat' => 2],

            // -------------------------------------------------------
            // KELAS 3A
            // -------------------------------------------------------
            ['nis' => '3172582444',   'nama' => 'ADDARA FREDELLA RIDWAN',                'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3166358934',   'nama' => 'AHMAD BELVA GHIFARI',                   'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3178946859',   'nama' => 'AILSA RIZQY NARESWARI',                 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3176921229',   'nama' => 'AISHA RAIHANAH ZAFIRAH',                'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3169292845',   'nama' => 'AKHTARRAYAN MULYA',                     'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3164790705',   'nama' => 'ALKHALIFI ZIKRI HADY SUPRIANTO',        'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3167605558',   'nama' => 'AXELA KHALVANY PAMUJI',                 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3163485125',   'nama' => 'BAIM AGATHA SETIAWAN',                  'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3178870377',   'nama' => 'CHERYL KIRANA HANANIA',                 'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3173520240',   'nama' => 'DYANDRA KENZO ABINAYA SUKERNO',         'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3168416359',   'nama' => 'ESHAN BUDI PRATAMA',                    'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3168699182',   'nama' => 'FATHIYA SHANUM',                        'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3171078386',   'nama' => 'HAFIZH PRADANA PUTRA',                  'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3161137017',   'nama' => 'KENZIE RAFFASYA ALFAREZKY',             'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3163864356',   'nama' => 'KHALIQA AZZALEA YHUNOV',                'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3161881866',   'nama' => 'MIKAILA KHALVANY PAMUJI',               'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3161928900',   'nama' => 'MUHAMMAD ABDURRAHMAN AL FATH',          'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3175569716',   'nama' => 'NAILA KHAERUNIVA IZZATI',               'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3165477096',   'nama' => 'RAFFASYA EL FARESKY ALAWI',             'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3160422994',   'nama' => 'REYNAND ALTHAF RAJENDRA',               'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3155461618',   'nama' => 'RIZKY SOPYANDI',                        'kelas' => '3A', 'tingkat' => 3],
            ['nis' => null,           'nama' => 'SHIVA MERISYKA PUTRI',                  'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3160703264',   'nama' => 'UKASYAH ALFATTAH',                      'kelas' => '3A', 'tingkat' => 3],
            ['nis' => '3167107320',   'nama' => 'WYDYA ALIFA NURAULIA MAKU SURO',        'kelas' => '3A', 'tingkat' => 3],

            // -------------------------------------------------------
            // KELAS 3B
            // -------------------------------------------------------
            ['nis' => '3162407169',   'nama' => 'ABDULLAH ROSYIQUL ABID',                'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3167032408',   'nama' => 'ABIDAH DHIA SYARAFANA',                 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3169154884',   'nama' => 'ADI PUTRA JAYA',                        'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3160878456',   'nama' => 'ADIBA ZAHIRA SYAUQI',                   'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3178408310',   'nama' => 'ADISTY SARATU JOHAR',                   'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3160999346',   'nama' => 'AISYAH HUMAIRA WAHYUDI',                'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3160279452',   'nama' => 'ALESHA ZAHRA',                          'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3167720543',   'nama' => 'AQILLA RAESHA SALSABILLA',              'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3178360992',   'nama' => 'ARSYA FAIREL ATHARIZZ',                 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3165294434',   'nama' => 'ATALLA EGAR RAKSYAKA',                  'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3173260852',   'nama' => 'AUFA RADEYA VALERIAN',                  'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3170023688',   'nama' => 'AYUNINDIA GENDHIS YUHASMOKO',           'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3169480039',   'nama' => 'DAI TAFTAJANI',                         'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3169482300',   'nama' => 'DEHAN ALFARIZKI',                       'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3177809818',   'nama' => 'DZAKIRA TALITA ZAHRA',                  'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3167503526',   'nama' => 'FAIREL ATHARIZ WAHYUDI',                'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3166825942',   'nama' => 'GIBRAN ARVINO FAEYZA KURNIAWAN',        'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3169613655',   'nama' => 'IBRAHIM GIFFARI ASSAUKI',               'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3168083299',   'nama' => 'KISYA RAHMA ISLAMI YERUSA',             'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3165714126',   'nama' => 'NAILA PUTRI HABBILLAH',                 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3162998993',   'nama' => 'NATASYA SULIS SUCIPTO',                 'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3168019436',   'nama' => 'RAKKA PUTRA WIJAYA',                    'kelas' => '3B', 'tingkat' => 3],
            ['nis' => '3169755900',   'nama' => 'SOFIA ZIDNI ALMAHYRA',                  'kelas' => '3B', 'tingkat' => 3],
            ['nis' => null,           'nama' => 'AISYAH NUHA ZAHIRA',                    'kelas' => '3B', 'tingkat' => 3],

            // -------------------------------------------------------
            // KELAS 3C
            // -------------------------------------------------------
            ['nis' => '3169291617',   'nama' => 'AFSHEEN SYIFA AN NISA',                 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3169032630',   'nama' => 'AIRANI NAURA ARDANI',                   'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3171743050',   'nama' => 'ALFARIZKA KARINA PUTRI',                'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3166096603',   'nama' => 'ALMEERA ALFATHUNISSA AZZAHRA',          'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3162278169',   'nama' => 'ANUGRAH HAKIM',                         'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3160800411',   'nama' => 'ARKA AMAR HIDAYAT',                     'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3176524948',   'nama' => 'ARSAKHA PRADIPTA ABQARI',               'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3167132678',   'nama' => 'ARSYILA NADHIFA AISHA',                 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3168096317',   'nama' => 'AULIYA IZZATUNNISA',                    'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3170425878',   'nama' => 'AZZAHRA ADYAH PUTRI',                   'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3179067090',   'nama' => 'DEFIN VERDIANTO',                       'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3170134367',   'nama' => 'ERLYTA ANINDYA',                        'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3163725137',   'nama' => 'FATHIA RAHMA FAZIA',                    'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3163541793',   'nama' => 'KINANTY ASHEEQA DIEFA',                 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3165514219',   'nama' => 'LUTFI MAHYA HERMAWAN',                  'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3160556962',   'nama' => 'M.FAEYZA TSANY MUBAROK',                'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3176146856',   'nama' => 'MUHAMMAD ANIS HASANUDIN',               'kelas' => '3C', 'tingkat' => 3],
            ['nis' => null,           'nama' => 'MUHAMMAD AZZAM AL FATIH',               'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3164185575',   'nama' => 'MUSYAARI RASYID SYIHAB',                'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3169264476',   'nama' => 'RAFASYA SIDDIQ BARUS',                  'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3166855242',   'nama' => 'SYAFIQ HANAN FADLILAH',                 'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3164430457',   'nama' => 'TARISAH AULA KAHIDA',                   'kelas' => '3C', 'tingkat' => 3],
            ['nis' => '3169776262',   'nama' => 'ZAIN AHMAD',                            'kelas' => '3C', 'tingkat' => 3],

            // -------------------------------------------------------
            // KELAS 4A
            // -------------------------------------------------------
            ['nis' => '3156145636',   'nama' => 'ABQORI ARRAFIF ARSENIO',                'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3153763025',   'nama' => 'ABBAS ZAIN ASH SHIDDIQ',                'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3154008189',   'nama' => 'ADITYA RAMADHAN',                       'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3156594249',   'nama' => 'ADNAN FAIZH PRATAMA',                   'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '0134260382',   'nama' => 'AKIFA NAILA AHMADI',                    'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3156863929',   'nama' => 'ALI ARASY RAMADHAN',                    'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3167190375',   'nama' => 'ALTHAF DWI PERMANA',                    'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3165817441',   'nama' => 'AULIA IZZATUNNISA',                     'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3166779562',   'nama' => 'AZIZAH SHAKILA ZAHRA',                  'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3156414983',   'nama' => 'DZAFRANSYAH NURRO PUTRA',               'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3150719172',   'nama' => 'FAEYZA THALITA FARZANA',                'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3152653283',   'nama' => 'FARZAN ATHALLA',                        'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3151973221',   'nama' => 'HAFIDZ NUMAN RIFAI',                    'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3153090217',   'nama' => 'KIANU LINTANG PERMANA',                 'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3157549444',   'nama' => 'KIRANA FILIA NATHASYIFANA',             'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3167973283',   'nama' => 'MUHAMMAD FARREL AR RASYID',             'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3154889911',   'nama' => 'MUTHIA LATHIFA ZAHRA',                  'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3156843151',   'nama' => 'NAWA HIDNA KIRANA',                     'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3155313540',   'nama' => 'RAIIFHA NADHINE SHIDQIYYA',             'kelas' => '4A', 'tingkat' => 4],
            ['nis' => '3150284605',   'nama' => 'SEKAR AZIZAH',                          'kelas' => '4A', 'tingkat' => 4],
            ['nis' => null,           'nama' => 'YASMIN NUR ASSYIFA',                    'kelas' => '4A', 'tingkat' => 4],

            // -------------------------------------------------------
            // KELAS 4B
            // -------------------------------------------------------
            ['nis' => '0161377016',   'nama' => 'ADEEVA AYUDIA INARA',                   'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3159023707',   'nama' => 'ANILA HASNA SOFYANA',                   'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3161506136',   'nama' => 'ARDENI NABIL PRADIPTA',                 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3157525980',   'nama' => 'ARINA MIKAILA SAKHI',                   'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3156930921',   'nama' => 'AZKANIA SASHI KIRANA',                  'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3155930176',   'nama' => 'DZAKIYYA MUNA AZIZAH',                  'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3150186157',   'nama' => 'FARID SIDIK PERMANA',                   'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3156799004',   'nama' => 'GALANG ADIPUTRA SURYATAMA',             'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3167387494',   'nama' => 'GENDIS MAHESWARI WIBOWO',               'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3150789584',   'nama' => 'HANIFAH FITRI ARMIZA',                  'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3153606938',   'nama' => 'KHAIRA PITALOKA',                       'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3158794638',   'nama' => 'MOCHAMMAD AZKA ADHITYA',                'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3153070079',   'nama' => 'MUHAMMAD FATHUL ISLAM',                 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3152281684',   'nama' => 'NAILA HERMANSYAH PUTRI',                'kelas' => '4B', 'tingkat' => 4],
            ['nis' => null,           'nama' => 'NAJMA ORLIN RAMADHANTY',                'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3150662932',   'nama' => 'NAVARA ADZKIYYAH ADZRA',                'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3165609049',   'nama' => 'RE INARA AYAKA MAULIDIA',               'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3169866087',   'nama' => 'UKHTIA RAHMA DESWANTI',                 'kelas' => '4B', 'tingkat' => 4],
            ['nis' => '3155513854',   'nama' => 'WILDAN FIRDAUS',                        'kelas' => '4B', 'tingkat' => 4],

            // -------------------------------------------------------
            // KELAS 4C
            // -------------------------------------------------------
            ['nis' => '3156281351',   'nama' => 'ABIYASA RASKIA SANTANA',                'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3153497805',   'nama' => 'ADAM AIRDAN HABIBIE',                   'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3159158445',   'nama' => 'ADIBA SALSABILA FATHIYATUROKHMA',       'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3155591517',   'nama' => 'AFIFAH NADHIF ATIKAH',                  'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3163668943',   'nama' => 'AHMAD FAEYZA DAFIQ IBRAHIM',            'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '0155560225',   'nama' => 'ALESHA NAURA CHANTIKA',                 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3151995000',   'nama' => 'ANTONY TRISTAN AL JARAS',               'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3151473373',   'nama' => 'ASYIFA AULIA AZZAHRA',                  'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3162007804',   'nama' => 'AZALEA KHALIQA DZAHIN',                 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3165133844',   'nama' => 'DZAKIYA NUR AZIZAH',                    'kelas' => '4C', 'tingkat' => 4],
            ['nis' => null,           'nama' => 'FAEYZA ARKANANTA PRANAJA',              'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3162795283',   'nama' => 'FALIH NOER UBAIDILLAH',                 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3159527690',   'nama' => 'FIKRI AHMAD HAMDHANI',                  'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3158518036',   'nama' => 'HAURA NUR AKILA ASMORO',                'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '0133752015',   'nama' => 'LARISA WAHYU HADZIQAH',                 'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3158621148',   'nama' => 'MUHAMMAD EL QIANO RAMADAN',             'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3152820989',   'nama' => 'NAJMA NUR SAFFANAH FEBRIAWAN',          'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '3152427578',   'nama' => 'SALMA SALSABILA FIRDAUS',               'kelas' => '4C', 'tingkat' => 4],
            ['nis' => '0153017073',   'nama' => 'ZIAN AULIA',                            'kelas' => '4C', 'tingkat' => 4],

            // -------------------------------------------------------
            // KELAS 5A
            // -------------------------------------------------------
            ['nis' => '3140562603',   'nama' => 'AIKO ARTA PRADANA',                     'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3144071892',   'nama' => 'AJENG PRAMESWARI HAIDAH',               'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3142165247',   'nama' => 'ANNISA DIANDRA RAMADHANI',              'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3140463177',   'nama' => 'ASLAN RAMADENTA PUTRA ARYANTO',         'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3158820686',   'nama' => 'AZARINE YUSRA FAHIMA',                  'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '0159812639',   'nama' => 'GALIH SETIAGUNG PUTRA',                 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3143314872',   'nama' => 'GIBRAN ARTANABIL',                      'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '0143704181',   'nama' => 'HANAFI AL ZAFRAN',                      'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3148017887',   'nama' => 'IFFA ASTILA RAHMA',                     'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '0153871451',   'nama' => 'KIKANDRIA AULIA PUTRI',                 'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '0151850782',   'nama' => 'MUH BAGUS SATTOMO PUTRA',               'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '0156860680',   'nama' => 'MUHAMMAD DIRGHAM AL-AQSA',              'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '0148585809',   'nama' => 'NICI FAAZA QIRANI',                     'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '0149232639',   'nama' => 'OZIL ANDRA HERMAWAN',                   'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3140979203',   'nama' => 'REIHAN EKA RAMADHAN',                   'kelas' => '5A', 'tingkat' => 5],
            ['nis' => '3140972787',   'nama' => 'UWAIS ALFARUQ',                         'kelas' => '5A', 'tingkat' => 5],
            ['nis' => null,           'nama' => 'WIDYA ARIFIANA SALSABILLA',             'kelas' => '5A', 'tingkat' => 5],
            ['nis' => null,           'nama' => 'WINDRIYA ANINDITA',                     'kelas' => '5A', 'tingkat' => 5],

            // -------------------------------------------------------
            // KELAS 6A
            // -------------------------------------------------------
            ['nis' => '0138392674',   'nama' => 'ABYAN GIBRAM',                          'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0132602046',   'nama' => 'AHMAD LUTHFIE',                         'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3133203991',   'nama' => 'ALBI ALFATIH',                          'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0142513515',   'nama' => 'AQUINA KHAIRA SHANUM',                  'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3130787079',   'nama' => 'ARGYA FAREL PURNOMO',                   'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3134609402',   'nama' => 'ARYASATYA ARDHANI',                     'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0131150384',   'nama' => 'AYUNINDYA IZZATUNNISA',                 'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0138596769',   'nama' => 'AZKA ALDRICH BARUS',                    'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0137171187',   'nama' => 'CHAIRUNNISA MAULIDA',                   'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3135656890',   'nama' => 'DAIBA ZIYA QABILA ZAHRA',               'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3141625970',   'nama' => 'DIFFA MUHAMMAD RIZKY',                  'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3137642651',   'nama' => 'FIRLY NAEEMA PUTRI',                    'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3135013061',   'nama' => 'GIZRA BUDI SEPTRIANDRY',                'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0135346991',   'nama' => 'IZYAN NAIZAR',                          'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '0132685532',   'nama' => 'KHANSA ALESHA LAUZAH JUNIT',            'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3139410979',   'nama' => 'PERMATA ARDHANA IZDIHAR',               'kelas' => '6A', 'tingkat' => 6],
            ['nis' => '3139464434',   'nama' => 'ZACKI ALVAN FADLI',                     'kelas' => '6A', 'tingkat' => 6],

            // -------------------------------------------------------
            // KELAS 6B
            // -------------------------------------------------------
            ['nis' => null,           'nama' => 'BINTANG ARDIKA',                        'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '3138891480',   'nama' => 'HAIKAL ASSYAIDI YUSUF',                 'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '3133381073',   'nama' => 'M. AZKA ULINNUHA',                      'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '0146935939',   'nama' => 'M. FATIYAN NAJMI ALNONI',               'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '3130291326',   'nama' => 'MARSYA LEANDRA GUSTIAMAN',              'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '0134169843',   'nama' => 'MUHAMMAD FACHRI HAMIZAN',               'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '3133375978',   'nama' => 'MUHAMMAD HAMDAN SYUKUR',                'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '0135462289',   'nama' => 'MUHAMMAD NAUFAL AFKAR',                 'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '3148613742',   'nama' => 'NAUFAL BAGUS WIJAYA',                   'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '3145325965',   'nama' => 'NEYSA INDAH KHAIRANI',                  'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '3137864545',   'nama' => 'NUR ALIFA RIZQIA SARI',                 'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '0143848892',   'nama' => 'QUEEN ATHALIA KHAIRUNNISA',             'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '3148520908',   'nama' => 'QUEENDY AMABELLE RISKI',                'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '0139840032',   'nama' => 'QUINNESYA ALZENA',                      'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '0135896433',   'nama' => 'SHABIL GHASANI NUGROHO',                'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '3139978748',   'nama' => 'SULTAN AHMAD FAUZAN',                   'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '0144630423',   'nama' => 'SULTAN BINTANG PRATAMA ARDIANSYAH',     'kelas' => '6B', 'tingkat' => 6],
            ['nis' => '0138543151',   'nama' => 'SULTHAN MUHAMMAD AL FATIH',             'kelas' => '6B', 'tingkat' => 6],
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
                'tahun_ajaran'    => '2025/2026',
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
