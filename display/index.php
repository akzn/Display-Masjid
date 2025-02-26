<?php
	// var_dump(PHP_OS);
	// die;
	$file	= '../db/database.json';
	if (!file_exists($file)){
		echo "<h1>Jalankan admin terlebih dahulu</h1>";
		die;
	}
	$json 	= file_get_contents($file);
	$db		= json_decode($json, true);
	$showDb	= $db;
	unset($showDb['akses']);
	
	$info_timer			= $db['timer']['info'] 		* 1000;	//detik
	$wallpaper_timer	= $db['timer']['wallpaper'] * 1000;	
	$adzan_timer		= $db['timer']['adzan'] 	* 1000 * 60; //menit
	// $iqomah_timer		= $db['timer']['iqomah'] 	* 1000 * 60;
	$sholat_timer		= $db['timer']['sholat'] 	* 1000 * 60;
	
	//optional
	$khutbah_jumat		= $db['jumat']['duration'] 	* 1000 * 60;
	$sholat_tarawih		= $db['tarawih']['duration'] 	* 1000 * 60;
	
	//Logo
	// nge trik ==> kalo replace file, di display logo yang lama masih kesimpen di cache ==> solusi ganti logo ganti nama file 
	$dirLogo	= 'logo/';
	$filesLogo	= array_diff(scandir($dirLogo),array('.','..','Thumbs.db'));
	$filesLogo	= array_values($filesLogo);//re index
	$logo		= $filesLogo[0];
	
	
	$dir	= 'wallpaper/';
	$files	= array_diff(scandir($dir),array('.','..','Thumbs.db'));
	$wallpaper	= '';
	$i	= 0;
	foreach($files as $v){
		$active	= $i==0?'active':'';
		$wallpaper	.= '<div class="item slides '.$active.'"><div style="background-image: url(wallpaper/'.$v.');"></div></div>';
		$i++;
	}
	// print_r($files);die;
?>


<!doctype html>
<html lang="en">
<head>
	<meta name="google" content="notranslate">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;500&display=swap" rel="stylesheet">
    <title>Display|Masjid</title>
    <link rel="icon" type="image/png" href="../icon.png"/>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
	<style>
		#running-text{
			height: 11vh;
		}
		#running-text .item{
			font-size: 4rem;
		}
		#running-text .item marquee{
			vertical-align: middle;
		}
		#running-text:before{
			height: 11vh;
		}
		#logo{
			bottom: 16vh;
		}

		#countdown-timer-mini{
			bottom: 16vh;
			position: relative;
			background-color:rgba(121, 248, 248, 0.69);
			/* padding: 5px; */
			border-radius: 20px;
			width: fit-content;
			display: flex;
			justify-content: flex-start;
			gap: 10px;
			position: fixed;
			right: 30px;
		}

		#countdown-timer-mini .timer{
			font-weight: 800;
			font-size: 2rem;
			font-family: 'Poppins', Helvetica, Arial, sans-serif;
			background-color:white;
			overflow: hidden;
			padding: 5px;
			border-top-right-radius: 20px;
			border-bottom-right-radius: 20px;
			border: 2px solid rgba(121, 248, 248, 0.69);
		}

		#countdown-timer-mini .icon{
			font-weight: 800;
			font-size: 2rem;
			display: flex;
			align-items: center;
			justify-content: center;
			padding-left:10px;
		}

		.quote-carousel .carousel-inner .item{
			height: 70vh;
		}

		.masjid-title-container{
			/* height:5vh; */
			padding:0.5em;
			background-color: rgba(0,0,0,0.5);
			color:#fff;
			font-family: 'Titillium Web', Poppins, Helvetica, Arial, sans-serif;
			text-align: end;
			display: flex;
			justify-content: flex-end;
		}

		.masjid-title-container .title{
			font-family: 'Roboto Slab', Helvetica, Arial, sans-serif;
			color:white;
			font-size:2em;
			font-weight: 600;
			/* letter-spacing: 2px; */
		}

		.masjid-title-container .sub-title{
			color:white;
			font-size:1.5em;
		}

		.masjid-title-container .icon{
			color:white;
			font-size: 4.5em;
			margin: 0 0.5em;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		@media screen and (min-width: 1920px){
			#running-text .item{
				font-size: 6rem;
			}

			.masjid-title-container .title{
				font-family: 'Roboto Slab', Helvetica, Arial, sans-serif;
				font-size:3em;
				font-weight: 600;
				letter-spacing: 2px;
			}

			.masjid-title-container .sub-title{
				font-size:2em;
			}
		}
	
	</style>
</head>

<body>
    <div id="preloader">
      <div id="status">&nbsp;</div>
    </div> 
	
	
	<div id="full-screen-clock" style="display:none"></div>
	<div id="count-down" class="full-screen" style="display:none">
		<div class="counter">
			<h1>COUNTER</h1>
			<div class="hh">00<span>JAM</span></div>
			<div class="ii">00<span>MENIT</span></div>
			<div class="ss">00<span>DETIK</span></div>
		</div>
	</div>
	<div id="display-adzan" class="full-screen" style="display:none"><div></div></div>
	<div id="display-sholat" class="full-screen" style="display:none"></div>
	<div id="display-khutbah" class="full-screen" style="display:none"><div></div></div>
	
	
	<div class="carousel fade-carousel slide" data-ride="carousel" data-interval="<?=$wallpaper_timer?>">
	  <!-- Overlay -->
	  <div class="overlay"></div>
	  <!-- Wrapper for slides -->
	  <div class="carousel-inner"><?=$wallpaper?></div> 
	</div>
	
	
	<div id="left-container">
		<div id="jam"></div>
		<div id="tgl"></div>
		<div id="jadwal"></div>
	</div>
	
	<div id="right-counter" style="display:none">
		<div class="counter">
			<h1>COUNTER</h1>
			<div class="hh">19<span>JAM</span></div>
			<div class="ii">25<span>MENIT</span></div>
			<div class="ss">45<span>DETIK</span></div>
		</div>
	</div>
	<div id="right-container">
		<div class="masjid-title-container">
			<div>
				<h5 class="title"><span style="color:#f1c863">Masjid</span> Jami' Achmad</h5>
				<h6 class="sub-title">Kelurahan Pejagoan, Kebumen, Jawa Tengah, 54361</h6>
			</div>
			<div class="icon"><i class="fas fa-mosque"></i></div>
		</div>
		<div id="quote">
			<div class="carousel quote-carousel slide" data-ride="carousel" data-interval="<?=$info_timer?>" data-pause="null">
			  <div class="carousel-inner">
				<?php 
				$i=0;
				foreach($db['info'] as $k => $v){
					if($v[3]){
						echo '
						<div class="item slides '.($i==0?'active':'').'">
						  <div class="hero">        
							<hgroup>
								<div class="text1" style="font-family: \'Titillium Web\', Helvetica, Arial, sans-serif;font-weight: 600;">'.htmlentities($v[0]).'</div>        
								<div class="text2">'.nl2br(htmlentities($v[1])).'</div>        
								<div class="text3">'.htmlentities($v[2]).'</div>
							</hgroup>
						  </div>
						</div>
						';
						$i++;
					}
				}
				?>
			  </div> 
			</div>
		</div>
		<!-- <div id="logo" style="background-image: url(logo/<?=$logo?>);"></div> -->
		<div id="countdown-timer-mini">
			<div class="icon"><i class="fas fa-clock"></i></div>
			<div class="timer"><span id="countdown-timer-mini-text">Maghrib</span> <span id="countdown-timer-mini-time">-04:00:40</span></div>
		</div>
		<div id="running-text">
			<div class="item">
				<!-- <div class="text"> -->
				<marquee>
				<?php 
					foreach($db['running_text'] as $k => $v){
						echo '<i class="fa fa-square-o" aria-hidden="true"></i> '.htmlentities($v);
					}
					// $ip 	= gethostbyname(php_uname('n'));	// PHP < 5.3.0
					$ip 	= gethostbyname(gethostname());		// PHP >= 5.3.0 ==> di linux keluar 127.0.0.1
					if(PHP_OS=='Linux'){
						//raspi 3
						// $command="/sbin/ifconfig wlan0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'";//raspi pake wlan0 jadi hotspot
						// $ip = exec ($command);
						
						//raspi 4
						$command="/sbin/ifconfig wlan0 | grep 'inet '| cut -d 't' -f2 | cut -d 'n' -f1 | awk '{ print $1}'";//raspi pake wlan0 jadi hotspot
						$ip = trim(exec ($command));
					}
					if($db['akses']['pass']=='admin'){
						echo '<i class="fa fa-square-o" aria-hidden="true"></i> Konek ke wifi (SSID: DisplayMasjid, password: 12345678)';
						echo '<i class="fa fa-square-o" aria-hidden="true"></i> Alamat admin http://'.$ip.'/';
						echo '<i class="fa fa-square-o" aria-hidden="true"></i> Default akses user : admin, password : admin';
						echo '<i class="fa fa-square-o" aria-hidden="true"></i> Silakan mengganti password admin untuk menghilangkan tulisan ini';
					}
				?>
				</marquee>
				<!-- </div> -->
			</div>
		</div>
	</div>
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/moment-with-locales.js"></script>
    <script src="js/PrayTimes.js"></script>
    <script src="js/jquery.marquee.js"></script>
    <script>
		<?php //Biar nggak ke load di HTML
		// loader 
		// $(window).on('load', function(){ // makes sure the whole site is loaded
			//$('#status').fadeOut(); // will first fade out the loading animation
			// $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
			//$('body').delay(350).css({'overflow':'visible'});
		// })

		// moment.locale('id');
		/*
		Input		Example			Description
		YYYY		2014			4 or 2 digit year
		YY			14				2 digit year
		Y			-25				Year with any number of digits and sign
		Q			1..4			Quarter of year. Sets month to first month in quarter.
		M MM		1..12			Month number
		MMM MMMM	Jan..December	Month name in locale set by moment.locale()
		D DD		1..31			Day of month
		Do			1st..31st		Day of month with ordinal
		DDD DDDD	1..365			Day of year
		X			1410715640.579	Unix timestamp
		x			1410715640579	Unix ms timestamp
		ddd dddd	Mon...Sunday	Day name in locale set by moment.locale()

		H HH		0..23			Hours (24 hour time)
		h hh		1..12			Hours (12 hour time used with a A.)
		k kk		1..24			Hours (24 hour time from 1 to 24)
		a A			am pm			Post or ante meridiem (Note the one character a p are also considered valid)
		m mm		0..59			Minutes
		s ss		0..59			Seconds
		S SS SSS	0..999			Fractional seconds
		Z ZZ		+12:00			Offset from UTC as +-HH:mm, +-HHmm, or Z


		*/
		?>
		
		
		
		//PrayTimes initialize
		var format 			= '24h';
		<?php
			echo "var lat 		= ".$db['setting']['latitude'].";\n";
			echo "var lng 		= ".$db['setting']['longitude'].";\n";
			echo "var timeZone 	= ".$db['setting']['timeZone'].";\n";
			echo "var dst 		= ".$db['setting']['dst'].";\n";
			
			
			$prayTimesAdjust	= [];
			if($db['prayTimesMethod']=='0'){
				foreach($db['prayTimesAdjust'] as $k => $v){
					if($v!='') $prayTimesAdjust[$k]=$v;
				}
				echo "var prayTimesAdjust =	$.parseJSON('".stripslashes(str_replace("`","\\`",json_encode($prayTimesAdjust)))."');\n";
				// echo "console.log(prayTimesAdjust);\n";
				echo "prayTimes.adjust(prayTimesAdjust);\n"; 
			}
			else {
				echo "prayTimes.setMethod('".$db['prayTimesMethod']."');\n";
			}
			
			$prayTimesTune	= [];
			foreach($db['prayTimesTune'] as $k => $v){
				if($v!='0') $prayTimesTune[$k]=$v;
			}
			if(count($prayTimesTune)>0){
				echo "var prayTimesTune =	$.parseJSON('".stripslashes(str_replace("`","\\`",json_encode($prayTimesTune)))."');\n";
				// echo "console.log(prayTimesTune);\n";
				echo "prayTimes.tune(prayTimesTune);\n"; 
			}
		?>
		
		
		
		//Baris ini ke bawah jika inget nanti pindah ke file terpisah biar rapi......
		
		var app	={
			db	: $.parseJSON(`<?=stripslashes(str_replace("`","\\`",json_encode($showDb)))?>`),
			cekDb	: false,
			tglHariIni		: '',
			tglBesok		: '',
			jadwalHariIni	: {},
			jadwalBesok		: {},
			timer			: false,
			// waitAdzanTimer	: false,	// Display countdown sebelum adzan
			adzanTimer		: false,	// Display adzan
			countDownTimer	: false,	// Display countdown iqomah
			sholatTimer		: false,	// Display sholat
			khutbahTimer	: false,	// Display khutbah
			nextPrayCount	: 0,		// start next pray count-down
			// nextPrayTimer	: false,	// Display countdown ke sholat selanjutnya
			fajr	: '',
			dhuhr	: '',
			asr		: '',
			maghrib	: '',
			isha	: '',
			audio	: new Audio('img/beep.mp3'),
			
			initialize	: function(){
				app.timer	= setInterval(function(){app.cekPerDetik()},1000);
				$('#preloader').delay(350).fadeOut('slow');

				// fetch setting every 5 minutes, first time load fetch
				app.fetchSetting();
				setInterval(function(){app.fetchSetting()},60000 * 5);

				// console.log(app.db);
				
				
				// let testTime	= moment().add(8,'seconds');
				// app.runRightCountDown(testTime,'Menuju dzuhur');
				// app.runFullCountDown(testTime,'iqomah',true);
				// app.runFullCountDown(testTime,'TEST COUNTER',false);
				// app.showDisplayAdzan('Dzuhur');
				// app.showDisplayKhutbah();
			},
			cekPerDetik	: function(){
				if(!app.tglHariIni || moment().format('YYYY-MM-DD') != moment(app.tglHariIni).format('YYYY-MM-DD')){
					app.tglHariIni	= moment();
					app.tglBesok 	= moment().add(1,'days');
					// console.log(app.tglHariIni);
					// console.log(app.tglBesok);
					app.jadwalHariIni	= app.getJadwal(moment(app.tglHariIni).toDate());
					app.jadwalBesok		= app.getJadwal(moment(app.tglBesok).toDate());
					// console.log(app.jadwalHariIni);
					// console.log(app.jadwalBesok);
					app.fajr	= moment(app.jadwalHariIni.fajr,'HH:mm');
					app.dhuhr	= moment(app.jadwalHariIni.dhuhr,'HH:mm');
					app.asr		= moment(app.jadwalHariIni.asr,'HH:mm');
					app.maghrib	= moment(app.jadwalHariIni.maghrib,'HH:mm');
					app.isha	= moment(app.jadwalHariIni.isha,'HH:mm');
					// console.log('fajr : '+app.fajr.format('YYYY-MM-DD HH:mm:ss'));
					// console.log('dhuhr : '+app.dhuhr.format('YYYY-MM-DD HH:mm:ss'));
					// console.log('asr : '+app.asr.format('YYYY-MM-DD HH:mm:ss'));
					// console.log('maghrib : '+app.maghrib.format('YYYY-MM-DD HH:mm:ss'));
					// console.log('isha : '+app.isha.format('YYYY-MM-DD HH:mm:ss'));
				}
				app.showJadwal();
				app.displaySchedule();
				// app.showCountDownNextPray();
				// app.runRightCountDown(app.dhuhr,'Dzuhur');
				app.runCountdownMini();
				
				// console.log('interval-1000');
			},
			getJadwal	: function(jadwalDate){
				let times = prayTimes.getTimes(jadwalDate, [lat, lng], timeZone, dst, format);
				return times;
			},
			showJadwal	: function(){
				// console.log(app.db.prayName)
				// let jamSekarang	= moment().add(9,'months');
				let jamSekarang	= moment();
				//+5 menit baru berubah yang aktif (misal sekarang jam dzuhur, di jadwal setelah 5 menit baru berubah yang ashar yang aktif)
				let jamDelay	= moment().subtract(5,'minutes');
				let jadwal	= '';
				let hari	= app.db.dayName[jamSekarang.format("dddd")];	//pastikan moment js pake standart inggris (default) ==> jangan pindah locale
				let bulan	= app.db.monthName[jamSekarang.format("MMMM")];
				
				// $('#tgl').html(moment().format("dddd, DD MMMM YYYY"));
				$('#jam').html(jamSekarang.format("HH.mm[<div>]ss[</div>]"));
				$('#tgl').html(jamSekarang.format("["+hari+"], DD ["+bulan+"] YYYY"));
				
				if($('.full-screen').is(":visible")){
					$('#full-screen-clock').html(jamSekarang.format("[<i class='fa fa-clock-o''></i>&nbsp;&nbsp;]HH:mm"));
					$('#full-screen-clock').slideDown();
					console.log('show');
				}
				else $('#full-screen-clock').slideUp();
				
				let jadwalDipake = app.jadwalHariIni;
				let jadwalPlusIcon	= '';
				//jika diatasa isya' pake jadwal besok
				
				// console.log(jamSekarang.format('YYYY-MM-DD HH:mm:ss'));
				if(jamDelay > app.isha){
					jadwalDipakeapp	= app.jadwalBesok;
					jadwalPlusIcon	= '<span><i class="fa fa-plus" aria-hidden="true"></i></span>';
					// console.log('besok');
				}
				$.each(app.db.prayName, function(k,v) {
					// console.log(jamDelay.format('YYYY-MM-DD HH:mm:ss'));
					let css= '';
					if		(k == 'isha' 	&& jamDelay < app.isha		&& jamDelay > app.maghrib) 	css= 'active';
					else if	(k == 'maghrib' && jamDelay < app.maghrib	&& jamDelay > app.asr) 		css= 'active';
					else if	(k == 'asr' 	&& jamDelay < app.asr		&& jamDelay > app.dhuhr) 	css= 'active';
					else if	(k == 'dhuhr' 	&& jamDelay < app.dhuhr		&& jamDelay > app.fajr) 	css= 'active';
					else if	(k == 'fajr'	&& (jamDelay < app.fajr		|| jamDelay > app.isha))	css= 'active';//diatas isha dan sebelum subuh (beda hari)
					jadwal += '<div class="row '+css+'"><div class="col-xs-5">'+v+'</div><div class="col-xs-7">'+jadwalDipake[k] + jadwalPlusIcon + '</div></div>';
				});
				$('#jadwal').html(jadwal);
			},
			displaySchedule: function(){
				// console.log(app.getNextPray());
				let waitAdzan		= moment().add(app.db.timer.wait_adzan,'minutes').format('YYYY-MM-DD HH:mm:ss');
				let jamSekarang		= moment().format('YYYY-MM-DD HH:mm:ss');
				
				// console.log(moment().add(5,'days').format('dddd'));
				// console.log(waitAdzan);
				// console.log(app.dhuhr.format('YYYY-MM-DD HH:mm:ss'));
				
				$.each(app.db.prayName, function(k,v) {
					//Normal 	: waitAdzanCountDown-adzan-iqomah-sholat-nextPrayCountDown
					//jumat 	: waitAdzanCountDown-adzan-khutbah-sholat-nextPrayCountDown
					//tarawih 	: waitAdzanCountDown-adzan-iqomah-sholat-isya-Tarawih(hanya durasi tarawih)-nextPrayCountDown
					
					let t			= moment(app[k]);//bikin variable baru t ==> jika ditulis let t	= app[k]; ==> jika di tambah / kurang, variable app[k] ikut berubah
					let jadwal		= t.format('YYYY-MM-DD HH:mm:ss');
					let stIqomah	= t.add(app.db.timer.adzan,'minutes').format('YYYY-MM-DD HH:mm:ss');
					let enIqomah	= moment(stIqomah,'YYYY-MM-DD HH:mm:ss').add(app.db.iqomah[k],'minutes')
					
					
					
					
					// console.log('Now-------------- '+jamSekarang);
					// console.log('time '+v+' : '+jadwal);
					// console.log('waitAdzan '+v+' : '+waitAdzan);
					// console.log('st iqomah '+v+' : '+stIqomah);
					// console.log('en iqomah '+v+' : '+enIqomah.format('YYYY-MM-DD HH:mm:ss'));
					if(waitAdzan == jadwal)				app.runRightCountDown(app[k],'Menuju '+v);	// CountDown sebelum adzan
					else if(jadwal == jamSekarang)		app.showDisplayAdzan(v);		// Display adzan
					else if(stIqomah == jamSekarang){
						if(moment().format('dddd')=='Friday' && app.db.jumat.active && k=='dhuhr'){
							//jumatan aktif skip iqomah --> waitAdzanCountDown-adzan-khutbah-sholat-nextPrayCountDown
							app.showDisplayKhutbah();
						}
						else
							app.runFullCountDown(enIqomah,'IQOMAH',true);	// CountDown iqomah
					} else if (moment().format('dddd')=='Friday' && app.db.jumat.active && moment().isSameOrAfter(moment(app.dhuhr._i, 'HH:mm'), 'minute') && moment().isSameOrBefore(moment(app.asr._i, 'HH:mm'), 'minute')) {
						// fix logic for jumatan
						let durasiKhutbah = app.db.jumat.duration // durasi in minutes
						let dhuhrTime = moment(app.dhuhr._i, 'HH:mm');
						let endTime = dhuhrTime.clone().add(durasiKhutbah, 'minutes');
						let currentTime = moment();
			
						if (currentTime.isSameOrBefore(endTime, 'minute')) {
							// khutbah aktif
							app.showDisplayKhutbah();
						} else {
							app.khutbahTimer	= false;
							app.showJadwal()
							$('#display-khutbah').fadeOut();
						}
					}
	
				});
				
		
				// let jamSekarang	= moment().add(5,'minutes');
				// if (!app.countDownTimer) {
					// app.runFullCountDown(jamSekarang,'IQOMAH');
				// }
			},
			getNextPray	: function(){
				let jamSekarang		= moment();
				let nextPray		= 'fajr';
				let jadwalDipake 	= false;
				if(jamSekarang > app.isha){
					jadwalDipake	= moment(app.jadwalBesok[nextPray],'HH:mm').add(1,'Day');
					console.log('jadwal besok');
				}
				else{
					$.each(app.db.prayName, function(k,v){
						if(jamSekarang < app[k]){
							nextPray	= k;
							return false;
						}
					});
					jadwalDipake	= moment(app.jadwalHariIni[nextPray],'HH:mm');
				}
				// console.log(jadwalDipake);
				return {
					'pray'	: nextPray,
					'date'	: jadwalDipake
				};
			},
			
			showCountDownNextPray	: function(){
				// $('#right-counter').html();
				let nextPray		= app.getNextPray();
				if (app.countDownTimer) return;//timer masih jalan
				app.nextPrayCount	= 0;
				console.log(moment(nextPray['date']).format('YYYY-MM-DD HH:mm:ss'));
				app.countDownTimer	= setInterval(function(){
					let t	= app.countDownCalculate(nextPray.date);
					
					$('#right-counter .counter>h1').html('Menuju '+app.db.prayName[nextPray.pray]);
					$('#right-counter .counter>.hh').html(t.hours+'<span>'+app.db.timeName.Hours+'</span>');
					$('#right-counter .counter>.ii').html(t.minutes+'<span>'+app.db.timeName.Minutes+'</span>');
					$('#right-counter .counter>.ss').html(t.seconds+'<span>'+app.db.timeName.Seconds+'</span>');
					
					$('#right-counter').slideDown();
					$('#quote').hide();
					
					app.nextPrayCount++;
					if (app.nextPrayCount >= 30) { // 30 detik show counter
						clearInterval(app.countDownTimer);
						app.countDownTimer	= false;
						$('#right-counter').fadeOut();
						$('#quote').fadeIn();
						// document.getElementById("demo").innerHTML = "EXPIRED";
					}
				},1000);
			},
			showDisplayAdzan	: function(prayName){
				if (!app.adzanTimer){
					$('#display-adzan>div').text(prayName);
					$('#display-adzan').show();
					app.adzanTimer	= setTimeout(function(){
						$('#display-adzan').fadeOut();
						app.adzanTimer	= false;
					},(app.db.timer.adzan * 60 * 1000)+1500);// to menit + 1.5 detik (remove jeda dengan iqomah)
				}
			},
			showDisplayKhutbah	: function(){
				if (!app.khutbahTimer){
					$('#display-khutbah>div').text(app.db.jumat.text);
					$('#display-khutbah').show();
					app.khutbahTimer	= setTimeout(function(){
						app.khutbahTimer	= false;
						app.showDisplaySholat();
						$('#display-khutbah').fadeOut();
					},app.db.jumat.duration * 60 * 1000);// to menit
				}
			},
			showDisplaySholat	: function(){
				if (!app.khutbahTimer){
					//cek tarawih
					let jamSekarang		= moment();
					let duration		= (jamSekarang > app.isha && app.db.tarawih.active)?app.db.tarawih.duration:app.db.timer.sholat;
					$('#display-sholat').show();
					app.khutbahTimer	= setTimeout(function(){
						$('#display-sholat').fadeOut();
						app.khutbahTimer	= false;
						app.showCountDownNextPray();
					},duration * 60 * 1000);// to menit
				}
			},
			runFullCountDown: function(jam,title,runDisplaySholat){
				// clearInterval(app.countDownTimer);
				if (app.countDownTimer) return;//timer masih jalan
				app.countDownTimer	= setInterval(function(){
					let t	= app.countDownCalculate(jam);
					
					$('#count-down .counter>h1').html(title);
					$('#count-down .counter>.hh').html(t.hours+'<span>'+app.db.timeName.Hours+'</span>');
					$('#count-down .counter>.ii').html(t.minutes+'<span>'+app.db.timeName.Minutes+'</span>');
					$('#count-down .counter>.ss').html(t.seconds+'<span>'+app.db.timeName.Seconds+'</span>');
					
					$('#count-down').fadeIn();
					if(t.distance==5){
						app.audio.play().then( () => {
						  // already allowed
						}).catch( () => {
							console.log('Agar beep bunyi ==> permission chrome : sound harus enable');
						});
						// audio.play();
					}
					if (t.distance < 1) {
						clearInterval(app.countDownTimer);
						app.countDownTimer	= false;
						$('#count-down').fadeOut();
						if(runDisplaySholat){
							app.showDisplaySholat();
						}
						// document.getElementById("demo").innerHTML = "EXPIRED";
					}
				},1000);
			},
			runRightCountDown	: function(jam,title){
				// $('#right-counter').html();
				if (app.countDownTimer) return;//timer masih jalan
				app.countDownTimer	= setInterval(function(){
					let t	= app.countDownCalculate(jam);
					
					$('#right-counter .counter>h1').html(title);
					$('#right-counter .counter>.hh').html(t.hours+'<span>'+app.db.timeName.Hours+'</span>');
					$('#right-counter .counter>.ii').html(t.minutes+'<span>'+app.db.timeName.Minutes+'</span>');
					$('#right-counter .counter>.ss').html(t.seconds+'<span>'+app.db.timeName.Seconds+'</span>');
					
					$('#right-counter').slideDown();
					$('#quote').hide();
					
					if (t.distance < 1) {
						clearInterval(app.countDownTimer);
						app.countDownTimer	= false;
						$('#right-counter').fadeOut();
						$('#quote').fadeIn();
						// document.getElementById("demo").innerHTML = "EXPIRED";
					}
				},1000);
			},
			runCountdownMini	: function(){
				let nextPray		= app.getNextPray();
				app.countDownTimer	= setInterval(function(){
					let t	= app.countDownCalculate(nextPray.date);

					let title = nextPray.pray;
					title = title.charAt(0).toUpperCase() + title.slice(1).toLowerCase();
					$('#countdown-timer-mini-text').html(title); 
					$('#countdown-timer-mini-time').html('-'+t.hours+':'+t.minutes+':'+t.seconds);
				}, 1000);
			},
			countDownCalculate(jam){
				let jamSekarang	= moment();//.subtract(2,'seconds');
				// console.log(jam.format('YYYY-MM-DD HH:mm:ss SSS'));
				// console.log(jamSekarang.format('YYYY-MM-DD HH:mm:ss SSS'));
				// --> jam.diff(jamSekarang, 'seconds') --> convert integer tanpa pembulatan (pembulatan ke bawah)
				let distance	= Math.round(jam.diff(jamSekarang, 'seconds', true)) ;
				// console.log(distance);
				let hours = Math.floor((distance % (60 * 60 * 24)) / (60 * 60));
				let minutes = Math.floor((distance % (60 * 60)) / 60);
				let seconds = Math.floor((distance % 60));
				hours	= (hours>=0		&& hours<10)	?'0'+hours:hours;
				minutes	= (minutes>=0	&& minutes<10)	?'0'+minutes:minutes;
				seconds	= (seconds>=0	&& seconds<10)	?'0'+seconds:seconds;
				// console.log(hours);
				return	{
					'distance'	: distance,
					'hours'		: hours,
					'minutes'	: minutes,
					'seconds'	: seconds
				};
			},

			// get update setting
			fetchSetting : function(){
				$.ajax({  
					type    : "POST",  
					url     : "../proses.php",
					dataType: "json",
					data    : {id:'changeDbCheck'}
				}).done(function(dt){
					// console.log(dt.data);
					if(app.cekDb==false) app.cekDb = dt.data;
					else if(app.cekDb !== dt.data) location.reload();
				}).fail(function(msg){
					console.log(msg);
				});
			}
		}
		app.initialize();
	</script>
</body>
</html>
