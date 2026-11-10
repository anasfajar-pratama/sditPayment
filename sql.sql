select * from pembayaran p where p.siswa_id =350 ;
select * from tagihan t where t.siswa_id = 350;
select * from siswa where nama like '%INTAN NOVIANTI%';

-- INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES('admin', 'web', '2026-07-25 02:28:09.000', '2026-07-25 02:28:09.000');
-- 
-- INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES('kasir', 'web', '2026-07-25 02:28:09.000', '2026-07-25 02:28:09.000');
-- 
-- INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES(1, 'App\Models\User', 1);
-- INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES(1, 'App\Models\User', 2);
-- INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES(1, 'App\Models\User', 3);
-- 
-- INSERT INTO users (name, email, email_verified_at, password, remember_token, created_at, updated_at) VALUES('Kasir', 'kasir@bungacempakarw.sch.id', NULL, '$2y$12$R20hl.6XdarXb8XeEtYP2u0PZ/YPsKv9WpJ8OJHGMYNXQtKlTUKJm', NULL, '2026-07-25 02:28:10.000', '2026-07-25 02:33:56.000');
-- INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES(2, 'App\Models\User', 4);

select a.id,a.nis,b.* from siswa a , tagihan b
WHERE a.nama like '%ABIL SHIDQI ARSALAN%'
and a.id = b.siswa_id ;
select * from pembayaran p where p.siswa_id in (536);
select * from tagihan a where a.siswa_id  = 564;
select * from kas_harians kh where kh.source = 'pembayaran' and kh.source_id IN (1462,2123,2124,2126) ;-- where kh.source 
select * from kas_harians kh where uraian like '%paud%';
select * from kas_harian_logs khl where kas_harian_id IN (3120,3121,3123);
select * from pembayaran p where p.id in (2123,2124,2126);
select * from siswa a where a.id in (464,450);
select * from kas_harians kh order by 1 desc limit 5;

select * from akun a ;

-- INSERT INTO `select a.id,a.nis,b.* from siswa a , tagihan b
-- WHERE a.nama like '%rajwa%'
-- and a.id = b.siswa_id ` (nis,siswa_id,jenis_pembayaran_id,bulan,tahun,nominal_tagihan,detail,status,created_at,updated_at) VALUES
-- 	 (NULL,393,NULL,NULL,'2026',1500000.00,'[{"jenis":"SPP","bulan":"08","tahun":"2026","nominal":1500000}]','belum_bayar','2026-08-03 14:43:02','2026-08-03 14:43:02');
select * from tagihan where id IN (513,514);
select * from tagihan where bulan IS NULL;

SELECT
        kh.id                                                              AS 
  kas_id,
        kh.source_id                                                       AS 
  pembayaran_id,
        kh.tanggal,
        p.siswa_id,
        s.nama                                                             AS 
  nama_seharusnya,
        CASE
            WHEN kh.uraian LIKE '% Kls %'
                THEN TRIM(SUBSTRING_INDEX(kh.uraian, ' Kls ', 1))
            ELSE TRIM(SUBSTRING_INDEX(kh.uraian, CONVERT(0xE28094 USING utf8mb4), 
  1))
        END                                                                AS 
  nama_tertulis,
        kh.uraian,
        kh.debit
    FROM kas_harians kh
    JOIN pembayaran p ON p.id = kh.source_id
    JOIN siswa      s ON s.id = p.siswa_id
    WHERE kh.source = 'pembayaran'
      AND kh.uraian NOT LIKE CONCAT(s.nama, '%')
    ORDER BY kh.tanggal, kh.source_id;
    
--    446	ARROYAN HERSA IBRAHIM	GIBRAN ARVINO FAEYZA KURNIAWAN
-- 446	ARROYAN HERSA IBRAHIM	GIBRAN ARVINO FAEYZA KURNIAWAN
-- 250	DZAKIYYA MUNA AZIZAH	GIBRAN ARVINO FAEYZA KURNIAWAN
-- 49	GIVANO SHIDQI ALTEZZA	GIBRAN ARVINO FAEYZA KURNIAWAN
-- 49	GIVANO SHIDQI ALTEZZA	GIBRAN ARVINO FAEYZA KURNIAWAN
-- 49	GIVANO SHIDQI ALTEZZA	GIBRAN ARVINO FAEYZA KURNIAWAN
-- 74	MUHAMMAD ARSHYA PRATAMA	GIBRAN ARVINO FAEYZA KURNIAWAN
-- 74	MUHAMMAD ARSHYA PRATAMA	GIBRAN ARVINO FAEYZA KURNIAWAN
   
--    330	2025-11-26	339	FATIMAH AZZAHRA (CANCEL)	FATIMAH AZZAHRA	FATIMAH AZZAHRA — Daftar Masuk Jul 2026
-- 331	2025-12-19	339	FATIMAH AZZAHRA (CANCEL)	FATIMAH AZZAHRA	FATIMAH AZZAHRA — Daftar Masuk Jul 2026
-- 1039	2026-06-05	132	ARSYILA LAUDY KEINARA	HANUM SUGIYA RUMAISHA	HANUM SUGIYA RUMAISHA Kls B — Daftar Ulang Jul 2026
-- 357	2026-06-22	392	ADIBA MIKAYLA AZZAHRA (PINDAHAN)	ADIBA MIKAYLA AZZAHRA	ADIBA MIKAYLA AZZAHRA — Daftar Masuk Jul 2026
-- 358	2026-06-22	390	RAJJAN ZARRA AR-RAJWA (PINDAHAN)	RAJJAN ZARRA AR-RAJWA	RAJJAN ZARRA AR-RAJWA — Daftar Masuk Jul 2026
-- 359	2026-06-23	390	RAJJAN ZARRA AR-RAJWA (PINDAHAN)	RAJJAN ZARRA AR-RAJWA	RAJJAN ZARRA AR-RAJWA — Daftar Masuk Jul 2026
-- 609	2026-07-24	160	BAIM AGATHA SETIAWAN	MUHAMMAD ALBI NUFAIL FAKHRI	MUHAMMAD ALBI NUFAIL FAKHRI Kls 3B — Daftar Ulang Jul 2026
-- 639	2026-07-26	4	AQMAR IZQIAN	DILFA NUR ZAMAN	DILFA NUR ZAMAN Kls - — Daftar Ulang Jul 2026
-- 640	2026-07-26	4	AQMAR IZQIAN	DILFA NUR ZAMAN	DILFA NUR ZAMAN Kls - — SPP Ags 2026
-- 642	2026-07-27	214	KINANTY ASHEEQA DIEFA	DILFA NUR ZAMAN	DILFA NUR ZAMAN Kls - — Daftar Ulang Jul 2026
-- 1080	2026-07-27	390	RAJJAN ZARRA AR-RAJWA (PINDAHAN)	RAJJAN ZARRA AR-RAJWA	RAJJAN ZARRA AR-RAJWA Kls 2B — SPP Ags 2026
-- 708	2026-07-29	129	ADE YUSUF AL KHAIR	ARKA AMAR HIDAYAT	ARKA AMAR HIDAYAT Kls 4C — Daftar Ulang Jul 2026
-- 735	2026-07-29	11	IKLIMA HANA TSABINA	MUHAMMAD RAIHAN HAMIZAN	MUHAMMAD RAIHAN HAMIZAN Kls 1D — SPP Ags 2026
-- 1027	2026-08-01	20	REFALINA AZKIYA SALSABILA	ADEEVA AYUDIA INARA	ADEEVA AYUDIA INARA Kls 5B — SPP Ags 2026
-- 1306	2026-08-28	390	RAJJAN ZARRA AR-RAJWA (PINDAHAN)	RAJJAN ZARRA AR-RAJWA	RAJJAN ZARRA AR-RAJWA Kls 2B — SPP September–September (1 bln)
-- 1944	2026-08-31	148	RHAIN HAFIZ ZAIDAN	ASLAN RAMADENTA PUTRA ARYANTO	ASLAN RAMADENTA PUTRA ARYANTO Kls 6A — SPP Ags 2026
-- 1653	2026-09-08	160	BAIM AGATHA SETIAWAN	ASHEEQA RIZQIA VIANDY (PINDAHAN)	ASHEEQA RIZQIA VIANDY (PINDAHAN) Kls - — SPP Ags 2026
-- 2103	2026-09-30	420	HABIBI ALRESCHA EL RUMI	ADNAN FAREL RADITYA	ADNAN FAREL RADITYA Kls - — SPP Okt 2026
-- 2104	2026-10-01	245	ADEEVA AYUDIA INARA	ADNAN FAREL RADITYA	ADNAN FAREL RADITYA Kls - — SPP Okt 2026
-- 2134	2026-10-05	452	NAIRA ALEENA	ALZENA SALSA BILUNA RAIQA	ALZENA SALSA BILUNA RAIQA Kls - — SPP Okt 2026
   
   select * from siswa where siswa.id = 160;
  select * from pembayaran p where siswa_id = 160;
   select * from kas_harians kh where kh.source_id IN (609);
   select * from pembayaran where id = 1654;