{{-- @extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Not Found')) --}}

<html>
<head>
	<title>Error 404</title>
	
	<style>
		body {
			 margin: 0;
			 font-size: 20px;
		}
		 * {
			 box-sizing: border-box;
		}
		 .container {
			 position: relative;
			 display: flex;
			 align-items: center;
			 justify-content: center;
			 height: 100vh;
			 background: white;
			 color: black;
			 font-family: arial, sans-serif;
			 overflow: hidden;
		}
		 .content {
			 position: relative;
			 width: 600px;
			 max-width: 100%;
			 margin: 20px;
			 background: white;
			 padding: 60px 40px;
			 text-align: center;
			 box-shadow: -10px 10px 67px -12px rgba(0, 0, 0, 0.2);
			 opacity: 0;
			 animation: apparition 0.8s 1.2s cubic-bezier(0.39, 0.575, 0.28, 0.995) forwards;
		}
		 .content p {
			 font-size: 1.3rem;
			 margin-top: 0;
			 margin-bottom: 0.6rem;
			 letter-spacing: 0.1rem;
			 color: #595959;
		}
		 .content p:last-child {
			 margin-bottom: 0;
		}
		 .content button {
			 display: inline-block;
			 margin-top: 2rem;
			 padding: 0.5rem 1rem;
			 border: 3px solid #595959;
			 background: transparent;
			 font-size: 1rem;
			 color: #595959;
			 text-decoration: none;
			 cursor: pointer;
			 font-weight: bold;
		}
		 .particle {
			 position: absolute;
			 display: block;
			 pointer-events: none;
		}
		 .particle:nth-child(1) {
			 top: 5.7971014493%;
			 left: 80.7392996109%;
			 font-size: 28px;
			 filter: blur(0.02px);
			 animation: 27s float infinite;
		}
		 .particle:nth-child(2) {
			 top: 31.067961165%;
			 left: 56.640625%;
			 font-size: 24px;
			 filter: blur(0.04px);
			 animation: 28s float infinite;
		}
		 .particle:nth-child(3) {
			 top: 19.3236714976%;
			 left: 65.1750972763%;
			 font-size: 28px;
			 filter: blur(0.06px);
			 animation: 21s floatReverse infinite;
		}
		 .particle:nth-child(4) {
			 top: 61.8404907975%;
			 left: 41.3793103448%;
			 font-size: 15px;
			 filter: blur(0.08px);
			 animation: 31s floatReverse infinite;
		}
		 .particle:nth-child(5) {
			 top: 2.9339853301%;
			 left: 62.8683693517%;
			 font-size: 18px;
			 filter: blur(0.1px);
			 animation: 33s floatReverse infinite;
		}
		 .particle:nth-child(6) {
			 top: 96.5517241379%;
			 left: 4.9407114625%;
			 font-size: 12px;
			 filter: blur(0.12px);
			 animation: 26s float2 infinite;
		}
		 .particle:nth-child(7) {
			 top: 73.8760631835%;
			 left: 83.0889540567%;
			 font-size: 23px;
			 filter: blur(0.14px);
			 animation: 22s float2 infinite;
		}
		 .particle:nth-child(8) {
			 top: 58.3232077764%;
			 left: 18.5728250244%;
			 font-size: 23px;
			 filter: blur(0.16px);
			 animation: 28s float2 infinite;
		}
		 .particle:nth-child(9) {
			 top: 91.3730255164%;
			 left: 77.2238514174%;
			 font-size: 23px;
			 filter: blur(0.18px);
			 animation: 29s float infinite;
		}
		 .particle:nth-child(10) {
			 top: 20.5630354957%;
			 left: 85.5457227139%;
			 font-size: 17px;
			 filter: blur(0.2px);
			 animation: 34s floatReverse2 infinite;
		}
		 .particle:nth-child(11) {
			 top: 84.8192771084%;
			 left: 13.5922330097%;
			 font-size: 30px;
			 filter: blur(0.22px);
			 animation: 40s floatReverse2 infinite;
		}
		 .particle:nth-child(12) {
			 top: 95.4489544895%;
			 left: 48.3711747285%;
			 font-size: 13px;
			 filter: blur(0.24px);
			 animation: 22s floatReverse2 infinite;
		}
		 .particle:nth-child(13) {
			 top: 86.1985472155%;
			 left: 23.3918128655%;
			 font-size: 26px;
			 filter: blur(0.26px);
			 animation: 29s float2 infinite;
		}
		 .particle:nth-child(14) {
			 top: 21.3851761847%;
			 left: 47.8983382209%;
			 font-size: 23px;
			 filter: blur(0.28px);
			 animation: 32s floatReverse infinite;
		}
		 .particle:nth-child(15) {
			 top: 54.1062801932%;
			 left: 93.3852140078%;
			 font-size: 28px;
			 filter: blur(0.3px);
			 animation: 30s float infinite;
		}
		 .particle:nth-child(16) {
			 top: 47.2906403941%;
			 left: 83.0039525692%;
			 font-size: 12px;
			 filter: blur(0.32px);
			 animation: 23s float2 infinite;
		}
		 .particle:nth-child(17) {
			 top: 64%;
			 left: 89.756097561%;
			 font-size: 25px;
			 filter: blur(0.34px);
			 animation: 26s floatReverse2 infinite;
		}
		 .particle:nth-child(18) {
			 top: 91.5129151292%;
			 left: 56.2685093781%;
			 font-size: 13px;
			 filter: blur(0.36px);
			 animation: 37s floatReverse2 infinite;
		}
		 .particle:nth-child(19) {
			 top: 32.9696969697%;
			 left: 72.1951219512%;
			 font-size: 25px;
			 filter: blur(0.38px);
			 animation: 33s floatReverse infinite;
		}
		 .particle:nth-child(20) {
			 top: 94.0606060606%;
			 left: 65.3658536585%;
			 font-size: 25px;
			 filter: blur(0.4px);
			 animation: 29s floatReverse2 infinite;
		}
		 .particle:nth-child(21) {
			 top: 66.1800486618%;
			 left: 90.0195694716%;
			 font-size: 22px;
			 filter: blur(0.42px);
			 animation: 21s float2 infinite;
		}
		 .particle:nth-child(22) {
			 top: 41.4957780458%;
			 left: 31.0981535471%;
			 font-size: 29px;
			 filter: blur(0.44px);
			 animation: 27s floatReverse2 infinite;
		}
		 .particle:nth-child(23) {
			 top: 78.0487804878%;
			 left: 80.3921568627%;
			 font-size: 20px;
			 filter: blur(0.46px);
			 animation: 25s floatReverse infinite;
		}
		 .particle:nth-child(24) {
			 top: 58.6797066015%;
			 left: 75.6385068762%;
			 font-size: 18px;
			 filter: blur(0.48px);
			 animation: 35s float2 infinite;
		}
		 .particle:nth-child(25) {
			 top: 6.8292682927%;
			 left: 82.3529411765%;
			 font-size: 20px;
			 filter: blur(0.5px);
			 animation: 37s float2 infinite;
		}
		 .particle:nth-child(26) {
			 top: 61.2393681652%;
			 left: 76.2463343109%;
			 font-size: 23px;
			 filter: blur(0.52px);
			 animation: 24s float2 infinite;
		}
		 .particle:nth-child(27) {
			 top: 38.8821385176%;
			 left: 65.4936461388%;
			 font-size: 23px;
			 filter: blur(0.54px);
			 animation: 23s floatReverse infinite;
		}
		 .particle:nth-child(28) {
			 top: 77.6699029126%;
			 left: 35.15625%;
			 font-size: 24px;
			 filter: blur(0.56px);
			 animation: 30s float2 infinite;
		}
		 .particle:nth-child(29) {
			 top: 47.3429951691%;
			 left: 20.4280155642%;
			 font-size: 28px;
			 filter: blur(0.58px);
			 animation: 30s float2 infinite;
		}
		 .particle:nth-child(30) {
			 top: 23.3009708738%;
			 left: 31.25%;
			 font-size: 24px;
			 filter: blur(0.6px);
			 animation: 34s float2 infinite;
		}
		 .particle:nth-child(31) {
			 top: 94.6859903382%;
			 left: 14.5914396887%;
			 font-size: 28px;
			 filter: blur(0.62px);
			 animation: 36s float infinite;
		}
		 .particle:nth-child(32) {
			 top: 16.6666666667%;
			 left: 84.6456692913%;
			 font-size: 16px;
			 filter: blur(0.64px);
			 animation: 27s floatReverse infinite;
		}
		 .particle:nth-child(33) {
			 top: 22.5766871166%;
			 left: 12.8078817734%;
			 font-size: 15px;
			 filter: blur(0.66px);
			 animation: 34s floatReverse infinite;
		}
		 .particle:nth-child(34) {
			 top: 62.4390243902%;
			 left: 64.7058823529%;
			 font-size: 20px;
			 filter: blur(0.68px);
			 animation: 38s float2 infinite;
		}
		 .particle:nth-child(35) {
			 top: 70.8737864078%;
			 left: 73.2421875%;
			 font-size: 24px;
			 filter: blur(0.7px);
			 animation: 29s floatReverse infinite;
		}
		 .particle:nth-child(36) {
			 top: 27.3504273504%;
			 left: 76.5456329735%;
			 font-size: 19px;
			 filter: blur(0.72px);
			 animation: 27s floatReverse infinite;
		}
		 .particle:nth-child(37) {
			 top: 0.9696969697%;
			 left: 29.2682926829%;
			 font-size: 25px;
			 filter: blur(0.74px);
			 animation: 21s float infinite;
		}
		 .particle:nth-child(38) {
			 top: 80.9756097561%;
			 left: 56.862745098%;
			 font-size: 20px;
			 filter: blur(0.76px);
			 animation: 25s floatReverse2 infinite;
		}
		 .particle:nth-child(39) {
			 top: 40.9257003654%;
			 left: 15.6709108717%;
			 font-size: 21px;
			 filter: blur(0.78px);
			 animation: 26s floatReverse2 infinite;
		}
		 .particle:nth-child(40) {
			 top: 23.2727272727%;
			 left: 30.243902439%;
			 font-size: 25px;
			 filter: blur(0.8px);
			 animation: 30s floatReverse infinite;
		}
		 .particle:nth-child(41) {
			 top: 71.3253012048%;
			 left: 16.5048543689%;
			 font-size: 30px;
			 filter: blur(0.82px);
			 animation: 35s float infinite;
		}
		 .particle:nth-child(42) {
			 top: 75.8620689655%;
			 left: 77.0750988142%;
			 font-size: 12px;
			 filter: blur(0.84px);
			 animation: 25s floatReverse2 infinite;
		}
		 .particle:nth-child(43) {
			 top: 94.6859903382%;
			 left: 70.0389105058%;
			 font-size: 28px;
			 filter: blur(0.86px);
			 animation: 26s float2 infinite;
		}
		 .particle:nth-child(44) {
			 top: 10.7975460123%;
			 left: 76.8472906404%;
			 font-size: 15px;
			 filter: blur(0.88px);
			 animation: 35s floatReverse2 infinite;
		}
		 .particle:nth-child(45) {
			 top: 68.4337349398%;
			 left: 87.3786407767%;
			 font-size: 30px;
			 filter: blur(0.9px);
			 animation: 28s floatReverse2 infinite;
		}
		 .particle:nth-child(46) {
			 top: 17.5824175824%;
			 left: 3.9254170756%;
			 font-size: 19px;
			 filter: blur(0.92px);
			 animation: 39s float infinite;
		}
		 .particle:nth-child(47) {
			 top: 23.2727272727%;
			 left: 3.9024390244%;
			 font-size: 25px;
			 filter: blur(0.94px);
			 animation: 40s floatReverse2 infinite;
		}
		 .particle:nth-child(48) {
			 top: 60.9336609337%;
			 left: 47.3372781065%;
			 font-size: 14px;
			 filter: blur(0.96px);
			 animation: 33s float infinite;
		}
		 .particle:nth-child(49) {
			 top: 95.6843403206%;
			 left: 46.4886251236%;
			 font-size: 11px;
			 filter: blur(0.98px);
			 animation: 25s floatReverse infinite;
		}
		 .particle:nth-child(50) {
			 top: 14.5102781137%;
			 left: 45.7643622201%;
			 font-size: 27px;
			 filter: blur(1px);
			 animation: 28s floatReverse infinite;
		}
		 .particle:nth-child(51) {
			 top: 31.9226118501%;
			 left: 20.4479065239%;
			 font-size: 27px;
			 filter: blur(1.02px);
			 animation: 30s floatReverse infinite;
		}
		 .particle:nth-child(52) {
			 top: 91.7073170732%;
			 left: 65.6862745098%;
			 font-size: 20px;
			 filter: blur(1.04px);
			 animation: 38s floatReverse infinite;
		}
		 .particle:nth-child(53) {
			 top: 78.1440781441%;
			 left: 32.3846908734%;
			 font-size: 19px;
			 filter: blur(1.06px);
			 animation: 32s floatReverse2 infinite;
		}
		 .particle:nth-child(54) {
			 top: 12.5452352232%;
			 left: 86.491739553%;
			 font-size: 29px;
			 filter: blur(1.08px);
			 animation: 35s float2 infinite;
		}
		 .particle:nth-child(55) {
			 top: 55.3398058252%;
			 left: 57.6171875%;
			 font-size: 24px;
			 filter: blur(1.1px);
			 animation: 24s float2 infinite;
		}
		 .particle:nth-child(56) {
			 top: 4.854368932%;
			 left: 88.8671875%;
			 font-size: 24px;
			 filter: blur(1.12px);
			 animation: 25s float infinite;
		}
		 .particle:nth-child(57) {
			 top: 69.5652173913%;
			 left: 95.3307392996%;
			 font-size: 28px;
			 filter: blur(1.14px);
			 animation: 27s floatReverse infinite;
		}
		 .particle:nth-child(58) {
			 top: 95.8435207824%;
			 left: 35.3634577603%;
			 font-size: 18px;
			 filter: blur(1.16px);
			 animation: 38s float infinite;
		}
		 .particle:nth-child(59) {
			 top: 26.6009852217%;
			 left: 37.5494071146%;
			 font-size: 12px;
			 filter: blur(1.18px);
			 animation: 24s floatReverse2 infinite;
		}
		 .particle:nth-child(60) {
			 top: 23.5582822086%;
			 left: 4.9261083744%;
			 font-size: 15px;
			 filter: blur(1.2px);
			 animation: 39s float2 infinite;
		}
		 .particle:nth-child(61) {
			 top: 7.7481840194%;
			 left: 94.5419103314%;
			 font-size: 26px;
			 filter: blur(1.22px);
			 animation: 22s floatReverse infinite;
		}
		 .particle:nth-child(62) {
			 top: 20.4628501827%;
			 left: 62.6836434868%;
			 font-size: 21px;
			 filter: blur(1.24px);
			 animation: 25s floatReverse2 infinite;
		}
		 .particle:nth-child(63) {
			 top: 88.1272949816%;
			 left: 19.6656833825%;
			 font-size: 17px;
			 filter: blur(1.26px);
			 animation: 30s float infinite;
		}
		 .particle:nth-child(64) {
			 top: 46.6585662211%;
			 left: 57.6735092864%;
			 font-size: 23px;
			 filter: blur(1.28px);
			 animation: 36s floatReverse infinite;
		}
		 .particle:nth-child(65) {
			 top: 22.2760290557%;
			 left: 17.5438596491%;
			 font-size: 26px;
			 filter: blur(1.3px);
			 animation: 33s floatReverse infinite;
		}
		 .particle:nth-child(66) {
			 top: 29.1616038882%;
			 left: 58.651026393%;
			 font-size: 23px;
			 filter: blur(1.32px);
			 animation: 37s float2 infinite;
		}
		 .particle:nth-child(67) {
			 top: 24.3013365735%;
			 left: 15.6402737048%;
			 font-size: 23px;
			 filter: blur(1.34px);
			 animation: 35s float2 infinite;
		}
		 .particle:nth-child(68) {
			 top: 78.239608802%;
			 left: 64.8330058939%;
			 font-size: 18px;
			 filter: blur(1.36px);
			 animation: 22s floatReverse2 infinite;
		}
		 .particle:nth-child(69) {
			 top: 42.0024420024%;
			 left: 44.1609421001%;
			 font-size: 19px;
			 filter: blur(1.38px);
			 animation: 40s floatReverse2 infinite;
		}
		 .particle:nth-child(70) {
			 top: 28.9855072464%;
			 left: 86.5758754864%;
			 font-size: 28px;
			 filter: blur(1.4px);
			 animation: 31s floatReverse infinite;
		}
		 .particle:nth-child(71) {
			 top: 30.243902439%;
			 left: 38.2352941176%;
			 font-size: 20px;
			 filter: blur(1.42px);
			 animation: 37s floatReverse2 infinite;
		}
		 .particle:nth-child(72) {
			 top: 9.7087378641%;
			 left: 31.25%;
			 font-size: 24px;
			 filter: blur(1.44px);
			 animation: 40s float2 infinite;
		}
		 .particle:nth-child(73) {
			 top: 2.9126213592%;
			 left: 17.578125%;
			 font-size: 24px;
			 filter: blur(1.46px);
			 animation: 36s float2 infinite;
		}
		 .particle:nth-child(74) {
			 top: 56.6544566545%;
			 left: 91.2659470069%;
			 font-size: 19px;
			 filter: blur(1.48px);
			 animation: 29s floatReverse2 infinite;
		}
		 .particle:nth-child(75) {
			 top: 21.7016029593%;
			 left: 87.0425321464%;
			 font-size: 11px;
			 filter: blur(1.5px);
			 animation: 35s float infinite;
		}
		 .particle:nth-child(76) {
			 top: 4.854368932%;
			 left: 34.1796875%;
			 font-size: 24px;
			 filter: blur(1.52px);
			 animation: 24s floatReverse infinite;
		}
		 .particle:nth-child(77) {
			 top: 61.4634146341%;
			 left: 94.1176470588%;
			 font-size: 20px;
			 filter: blur(1.54px);
			 animation: 25s float2 infinite;
		}
		 .particle:nth-child(78) {
			 top: 43.2964329643%;
			 left: 96.7423494571%;
			 font-size: 13px;
			 filter: blur(1.56px);
			 animation: 23s floatReverse2 infinite;
		}
		 .particle:nth-child(79) {
			 top: 64.039408867%;
			 left: 50.395256917%;
			 font-size: 12px;
			 filter: blur(1.58px);
			 animation: 21s float2 infinite;
		}
		 .particle:nth-child(80) {
			 top: 50.6082725061%;
			 left: 25.4403131115%;
			 font-size: 22px;
			 filter: blur(1.6px);
			 animation: 36s float2 infinite;
		}
		 @keyframes apparition {
			 from {
				 opacity: 0;
				 transform: translateY(100px);
			}
			 to {
				 opacity: 1;
				 transform: translateY(0);
			}
		}
		 @keyframes float {
			 0%, 100% {
				 transform: translateY(0);
			}
			 50% {
				 transform: translateY(180px);
			}
		}
		 @keyframes floatReverse {
			 0%, 100% {
				 transform: translateY(0);
			}
			 50% {
				 transform: translateY(-180px);
			}
		}
		 @keyframes float2 {
			 0%, 100% {
				 transform: translateY(0);
			}
			 50% {
				 transform: translateY(28px);
			}
		}
		 @keyframes floatReverse2 {
			 0%, 100% {
				 transform: translateY(0);
			}
			 50% {
				 transform: translateY(-28px);
			}
		}
	</style>
</head>
<body>

	<main class="container">
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">4</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  <span class="particle">0</span>
	  
	  <article class="content">
        <p><strong>Error 404</strong></p>
		<p>Halaman yang Anda tuju tidak ditemukan,</p>
		<p>
		  <button>Klik di bagian luar kotak popup ini untuk menutup, <br>atau Anda bisa memuat ulang</button>
		</p>
	  </article>
	</main>

</body>
</html>