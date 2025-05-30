<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Яндекс Карта</title>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=f2206a66-2fdf-432e-b948-9b64ddffc204&lang=ru_RU" type="text/javascript"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        html, body, #map {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #controls {
            position: fixed;
            bottom: 37px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 6px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        #controls input {
            padding: 4px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 11.5px;
            font-family: 'Inter', sans-serif !important;
            font-weight: 500;
        }

        #controls button {
            padding: 1px 6px;
            border: none;
            border-radius: 7px;
            background: #1E90FF;
            color: white;
            cursor: pointer;
            font-size: 11.5px;
            font-family: 'Inter', sans-serif !important;
            font-weight: 500;
        }

        #controls button:hover {
            background: #0056b3;
        }

        #controls select {
            padding: 1px 6px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 11.5px;
            font-family: 'Inter', sans-serif !important;
            font-weight: 500;
        }

        #messageBox {
            position: fixed;
            top: 50%; /* Размещение по вертикали на середине */
            left: 50%; /* Размещение по горизонтали на середине */
            transform: translate(-50%, -50%); /* Смещение на 50% по обеим осям, чтобы центрировать */
            background: rgba(50, 50, 50, 0.9);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            font-family: 'Inter', sans-serif !important;
            font-size: 12px;
            display: none;
            z-index: 1000;
        }


        #legend {
            position: fixed;
            bottom: 85px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            padding: 6px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            font-family: 'Inter', sans-serif !important;
            font-size: 10.5px;
            font-weight: 500;
            z-index: 1000;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            border-radius: 4px;
        }

        .legend-color.green {
            background-color: #00640013;
            border: 1px solid #00640080;
        }

        .legend-color.orange {
            background-color: #FF450013;
            border: 1px solid #FF450080;
        }
    </style>
</head>
<body>
<div id="map"></div>
<div id="controls">
    <input type="text" id="addressInput" placeholder="Введите адрес">
    <select id="routeType">
        <option value="pedestrian">Пешком</option>
        <option value="auto">Авто</option>
    </select>
    <button id="findRouteButton">Построить маршрут</button>
    <button id="autoButton">Отображение зон</button>
</div>
<div id="messageBox"></div>
<div id="legend">
    <div class="legend-item">
        <div class="legend-color green"></div>
        <span>Зоны покрытия лавок</span>
    </div>
    <div class="legend-item">
        <div class="legend-color orange"></div>
        <span>Зона пересечения лавок</span>
    </div>
</div>

<script>
    ymaps.ready(init);
    function init() {
        var map = new ymaps.Map("map", {
            center: [55.978746, 37.204738],
            zoom: 12,
            controls: ['zoomControl']
        });

        var zones = [];
        var markers = [];
        var route = null;
        var zonesVisible = false;
        // Координаты зон
        var PolygonCoords_1 = [
            [
                [56.016273, 37.131647],
                [56.009562, 37.139054],
                [56.009629, 37.139413],
                [55.999271, 37.151122],
                [55.999149, 37.150844],
                [55.996745, 37.153592],
                [55.994954, 37.155660],
                [55.994937, 37.155684],
                [55.995335, 37.156856],
                [55.997335, 37.156644],
                [55.995920, 37.162119],
                [55.994762, 37.166963],
                [55.995414, 37.170351],
                [55.995167, 37.173731],
                [55.996515, 37.174783],
                [55.996951, 37.177909],
                [55.997423, 37.180378],
                [55.999229, 37.186269],
                [55.999108, 37.187106],
                [55.999691, 37.186900],
                [56.002348, 37.186315],
                [56.003704, 37.187275],
                [56.006777, 37.191642],
                [56.011116, 37.194881],
                [56.010699, 37.202844],
                [56.012627, 37.210334],
                [56.013210, 37.210974],
                [56.017158, 37.207291],
                [56.017248, 37.206970],
                [56.016081, 37.203767],
                [56.015543, 37.203126],
                [56.021304, 37.188883],
                [56.020024, 37.187282],
                [56.020179, 37.184891],
                [56.019663, 37.184247],
                [56.019019, 37.185443],
                [56.017190, 37.182131],
                [56.016469, 37.179923],
                [56.015078, 37.177577],
                [56.014511, 37.169988],
                [56.013906, 37.167768],
                [56.014267, 37.164742],
                [56.014132, 37.164422],
                [56.013684, 37.163941],
                [56.014132, 37.162580],
                [56.013325, 37.161379],
                [56.013774, 37.158496],
                [56.014088, 37.158496],
                [56.014671, 37.159457],
                [56.015388, 37.158656],
                [56.016465, 37.159377],
                [56.018080, 37.158015],
                [56.017900, 37.155293],
                [56.016555, 37.150648],
                [56.017183, 37.149927],
                [56.018125, 37.145042],
                [56.017542, 37.143761],
                [56.018125, 37.138956],
                [56.017721, 37.138956],
                [56.018977, 37.135752],
                [56.016273, 37.131647]
            ]
        ];
        var PolygonCoords_2 = [
            [
                [55.999141, 37.150875],
                [55.998365, 37.149254],
                [55.996493, 37.150513],
                [55.996204, 37.151702],
                [55.994163, 37.152837],
                [55.993119, 37.152533],
                [55.993194, 37.150567],
                [55.992186, 37.148793],
                [55.991601, 37.149608],
                [55.991131, 37.149368],
                [55.990802, 37.147743],
                [55.991072, 37.146782],
                [55.989591, 37.144342],
                [55.977770, 37.148496],
                [55.977435, 37.148162],
                [55.977259, 37.147563],
                [55.975064, 37.147647],
                [55.975013, 37.148777],
                [55.970314, 37.148798],
                [55.970009, 37.146219],
                [55.966232, 37.145630],
                [55.966122, 37.146930],
                [55.965658, 37.148402],
                [55.963749, 37.146562],
                [55.963181, 37.149138],
                [55.962459, 37.149414],
                [55.962614, 37.150794],
                [55.961926, 37.151243],
                [55.962361, 37.153216],
                [55.963080, 37.153216],
                [55.963012, 37.154057],
                [55.962002, 37.154337],
                [55.962630, 37.156820],
                [55.962383, 37.157861],
                [55.962207, 37.157838],
                [55.962001, 37.158114],
                [55.961949, 37.159493],
                [55.962207, 37.159355],
                [55.962362, 37.158941],
                [55.962568, 37.158941],
                [55.962568, 37.159309],
                [55.962155, 37.160137],
                [55.961846, 37.159861],
                [55.961536, 37.160275],
                [55.961562, 37.160781],
                [55.961846, 37.160827],
                [55.961897, 37.161149],
                [55.961330, 37.161885],
                [55.960040, 37.161885],
                [55.960117, 37.162253],
                [55.960375, 37.162437],
                [55.960194, 37.162713],
                [55.959962, 37.162759],
                [55.959627, 37.162207],
                [55.959369, 37.162345],
                [55.959059, 37.163173],
                [55.959317, 37.163357],
                [55.959446, 37.163265],
                [55.959549, 37.164001],
                [55.959420, 37.164185],
                [55.959059, 37.163909],
                [55.959162, 37.165381],
                [55.959111, 37.165933],
                [55.958698, 37.165611],
                [55.958569, 37.165841],
                [55.958621, 37.167313],
                [55.958982, 37.167175],
                [55.959137, 37.167635],
                [55.959059, 37.168417],
                [55.958853, 37.168831],
                [55.958904, 37.169107],
                [55.959343, 37.169061],
                [55.959369, 37.168233],
                [55.959472, 37.168049],
                [55.959524, 37.168095],
                [55.959988, 37.168969],
                [55.960710, 37.168601],
                [55.960916, 37.170035],
                [55.960759, 37.171151],
                [55.960055, 37.173242],
                [55.959586, 37.173730],
                [55.959234, 37.173730],
                [55.958960, 37.172685],
                [55.958217, 37.172127],
                [55.957983, 37.172545],
                [55.957631, 37.174706],
                [55.957552, 37.175334],
                [55.958804, 37.177634],
                [55.958452, 37.178541],
                [55.958647, 37.179656],
                [55.958491, 37.181329],
                [55.958726, 37.181887],
                [55.958765, 37.182584],
                [55.958298, 37.182551],
                [55.958025, 37.183597],
                [55.958025, 37.183597],
                [55.956851, 37.183945],
                [55.956617, 37.184155],
                [55.955717, 37.183248],
                [55.955013, 37.184224],
                [55.954231, 37.183248],
                [55.953136, 37.186386],
                [55.955482, 37.187571],
                [55.956186, 37.188756],
                [55.955091, 37.194263],
                [55.954544, 37.193845],
                [55.953918, 37.195239],
                [55.954778, 37.195867],
                [55.953292, 37.202769],
                [55.951865, 37.202840],
                [55.949800, 37.201736],
                [55.949490, 37.201460],
                [55.949026, 37.201644],
                [55.948665, 37.202288],
                [55.948303, 37.204956],
                [55.948871, 37.207072],
                [55.949232, 37.208083],
                [55.948819, 37.208635],
                [55.948561, 37.208267],
                [55.948613, 37.209463],
                [55.948200, 37.210935],
                [55.948523, 37.211211],
                [55.948478, 37.211692],
                [55.948433, 37.211772],
                [55.948613, 37.212092],
                [55.948838, 37.211772],
                [55.948927, 37.212252],
                [55.948433, 37.213534],
                [55.948838, 37.214414],
                [55.948568, 37.215856],
                [55.948791, 37.215984],
                [55.948791, 37.216502],
                [55.948306, 37.216934],
                [55.948160, 37.217626],
                [55.950343, 37.219873],
                [55.951603, 37.218317],
                [55.953206, 37.219257],
                [55.954125, 37.220713],
                [55.955249, 37.218468],
                [55.957190, 37.219257],
                [55.957939, 37.217193],
                [55.956407, 37.215251],
                [55.956679, 37.213794],
                [55.955966, 37.212982],
                [55.955861, 37.212482],
                [55.956456, 37.211047],
                [55.956876, 37.210673],
                [55.959396, 37.209924],
                [55.959746, 37.209612],
                [55.961321, 37.206991],
                [55.961426, 37.206430],
                [55.963351, 37.206180],
                [55.963561, 37.206180],
                [55.964289, 37.209163],
                [55.967194, 37.211534],
                [55.968244, 37.209163],
                [55.968734, 37.208851],
                [55.968769, 37.207603],
                [55.969890, 37.208408],
                [55.970263, 37.206985],
                [55.971043, 37.203584],
                [55.971149, 37.202909],
                [55.972847, 37.194937],
                [55.970811, 37.193173],
                [55.971071, 37.190134],
                [55.970241, 37.182727],
                [55.970287, 37.175667],
                [55.972262, 37.175369],
                [55.975064, 37.171532],
                [55.974702, 37.170048],
                [55.973145, 37.167386],
                [55.972727, 37.165488],
                [55.971834, 37.163463],
                [55.970295, 37.158107],
                [55.972821, 37.159499],
                [55.974120, 37.158362],
                [55.974911, 37.157361],
                [55.975575, 37.156314],
                [55.975932, 37.154539],
                [55.975383, 37.152810],
                [55.976161, 37.151854],
                [55.977437, 37.152446],
                [55.978266, 37.152837],
                [55.978777, 37.154544],
                [55.979491, 37.156660],
                [55.980040, 37.157361],
                [55.980869, 37.158453],
                [55.981673, 37.159022],
                [55.982349, 37.158976],
                [55.983280, 37.158093],
                [55.984617, 37.157670],
                [55.985318, 37.158785],
                [55.984859, 37.162403],
                [55.985563, 37.163586],
                [55.987210, 37.164269],
                [55.989579, 37.161554],
                [55.991389, 37.159572],
                [55.993071, 37.157760],
                [55.994539, 37.156162],
                [55.994954, 37.155660],
                [55.999141, 37.150875]
            ]
        ];

        var PolygonCoords_3 = [
            [
                [55.980550, 37.225106],
                [55.980188, 37.226737],
                [55.980366, 37.229167],
                [55.979417, 37.229998],
                [55.978901, 37.231286],
                [55.979108, 37.231837],
                [55.978640, 37.232745],
                [55.978847, 37.233757],
                [55.979466, 37.234585],
                [55.979582, 37.235659],
                [55.979817, 37.235729],
                [55.979739, 37.236356],
                [55.979348, 37.236914],
                [55.979029, 37.238341],
                [55.979116, 37.239540],
                [55.979405, 37.240339],
                [55.978903, 37.241800],
                [55.978190, 37.242518],
                [55.977859, 37.243613],
                [55.977500, 37.244118],
                [55.976781, 37.243878],
                [55.975717, 37.246747],
                [55.970455, 37.253186],
                [55.968880, 37.252988],
                [55.967941, 37.252431],
                [55.967941, 37.252849],
                [55.968332, 37.254243],
                [55.967941, 37.254801],
                [55.967668, 37.257520],
                [55.967902, 37.260030],
                [55.967433, 37.261075],
                [55.967277, 37.261075],
                [55.967081, 37.262609],
                [55.967980, 37.268117],
                [55.968332, 37.267768],
                [55.968176, 37.261354],
                [55.968614, 37.261244],
                [55.969924, 37.261487],
                [55.969907, 37.261002],
                [55.971507, 37.261548],
                [55.971728, 37.262640],
                [55.971932, 37.262822],
                [55.971932, 37.263217],
                [55.972085, 37.263126],
                [55.973038, 37.264279],
                [55.975965, 37.265104],
                [55.978738, 37.266136],
                [55.979650, 37.266581],
                [55.979998, 37.264925],
                [55.980369, 37.265032],
                [55.981288, 37.265958],
                [55.982313, 37.267980],
                [55.982609, 37.267537],
                [55.982640, 37.266069],
                [55.982779, 37.265487],
                [55.982997, 37.265210],
                [55.983339, 37.265099],
                [55.984457, 37.265730],
                [55.984550, 37.265841],
                [55.984783, 37.265786],
                [55.986104, 37.265121],
                [55.987005, 37.264234],
                [55.987222, 37.264511],
                [55.987346, 37.263902],
                [55.988541, 37.263035],
                [55.987965, 37.260905],
                [55.987418, 37.255601],
                [55.988982, 37.254265],
                [55.989925, 37.257259],
                [55.991683, 37.259490],
                [55.993187, 37.260745],
                [55.997856, 37.248363],
                [55.998449, 37.248469],
                [55.998567, 37.248046],
                [55.999100, 37.247412],
                [56.000521, 37.246673],
                [56.000876, 37.246250],
                [56.001113, 37.245299],
                [56.001172, 37.244348],
                [56.000936, 37.243397],
                [56.000343, 37.242150],
                [56.006364, 37.226956],
                [56.005434, 37.225705],
                [56.005536, 37.219402],
                [56.003758, 37.218923],
                [55.999878, 37.217693],
                [55.998313, 37.217873],
                [55.997517, 37.216897],
                [55.997299, 37.218151],
                [55.996614, 37.218959],
                [55.995724, 37.217543],
                [55.995277, 37.215558],
                [55.994620, 37.215162],
                [55.993945, 37.215333],
                [55.992796, 37.215056],
                [55.992022, 37.217301],
                [55.991596, 37.221290],
                [55.991557, 37.224622],
                [55.991952, 37.224566],
                [55.992702, 37.225514],
                [55.991387, 37.226452],
                [55.990875, 37.227898],
                [55.989863, 37.228648],
                [55.989727, 37.230745],
                [55.989624, 37.232210],
                [55.989254, 37.233201],
                [55.988684, 37.233465],
                [55.988688, 37.235634],
                [55.987072, 37.234469],
                [55.985106, 37.237557],
                [55.985011, 37.231251],
                [55.985099, 37.229439],
                [55.984994, 37.228813],
                [55.984775, 37.228268],
                [55.984620, 37.227751],
                [55.984285, 37.227312],
                [55.983995, 37.227277],
                [55.983772, 37.227160],
                [55.983241, 37.227089],
                [55.982741, 37.226679],
                [55.982640, 37.226484],
                [55.982479, 37.226088],
                [55.982351, 37.225912],
                [55.981845, 37.225553],
                [55.981467, 37.225217],
                [55.981138, 37.225118],
                [55.980920, 37.224940]
            ]
        ];

        var PolygonCoords_peresecheniya = [
            [
                [56.012627, 37.210334],
                [56.010699, 37.202844],
                [56.011116, 37.194881],
                [56.006777, 37.191642],
                [56.003704, 37.187275],
                [56.002348, 37.186315],
                [55.999691, 37.186900],
                [55.999108, 37.187106],
                [55.999229, 37.186269],
                [55.997423, 37.180378],
                [55.996951, 37.177909],
                [55.996515, 37.174783],
                [55.995167, 37.173731],
                [55.995414, 37.170351],
                [55.994762, 37.166963],
                [55.995920, 37.162119],
                [55.997335, 37.156644],
                [55.995335, 37.156856],
                [55.994937, 37.155684],
                [55.994954, 37.155660],
                [55.994539, 37.156162],
                [55.993071, 37.157760],
                [55.991389, 37.159572],
                [55.989579, 37.161554],
                [55.987210, 37.164269],
                [55.985563, 37.163586],
                [55.984859, 37.162403],
                [55.985318, 37.158785],
                [55.984617, 37.157670],
                [55.983280, 37.158093],
                [55.982349, 37.158976],
                [55.981673, 37.159022],
                [55.980869, 37.158453],
                [55.980040, 37.157361],
                [55.979491, 37.156660],
                [55.978777, 37.154544],
                [55.978266, 37.152837],
                [55.977437, 37.152446],
                [55.976161, 37.151854],
                [55.975383, 37.152810],
                [55.975932, 37.154539],
                [55.975575, 37.156314],
                [55.974911, 37.157361],
                [55.974120, 37.158362],
                [55.972821, 37.159499],
                [55.970295, 37.158107],
                [55.971834, 37.163463],
                [55.972727, 37.165488],
                [55.973145, 37.167386],
                [55.974702, 37.170048],
                [55.975064, 37.171532],
                [55.972262, 37.175369],
                [55.970287, 37.175667],
                [55.970241, 37.182727],
                [55.971071, 37.190134],
                [55.970811, 37.193173],
                [55.972847, 37.194937],
                [55.971149, 37.202909],
                [55.971043, 37.203584],
                [55.970263, 37.206985],
                [55.969890, 37.208408],
                [55.970204, 37.208476],
                [55.969401, 37.212210],
                [55.970214, 37.212949],
                [55.970562, 37.211278],
                [55.976131, 37.212560],
                [55.976759, 37.212400],
                [55.976412, 37.214438],
                [55.976120, 37.214398],
                [55.976075, 37.215038],
                [55.976838, 37.215799],
                [55.976187, 37.216800],
                [55.977310, 37.218482],
                [55.977714, 37.217481],
                [55.977669, 37.216880],
                [55.978343, 37.217201],
                [55.980520, 37.218882],
                [55.980969, 37.219443],
                [55.981396, 37.220444],
                [55.981643, 37.221565],
                [55.981755, 37.222606],
                [55.981598, 37.223247],
                [55.981733, 37.223487],
                [55.981463, 37.224368],
                [55.980768, 37.224929],
                [55.980920, 37.224940],
                [55.981138, 37.225118],
                [55.981467, 37.225217],
                [55.981845, 37.225553],
                [55.982351, 37.225912],
                [55.982479, 37.226088],
                [55.982640, 37.226484],
                [55.982741, 37.226679],
                [55.983241, 37.227089],
                [55.983772, 37.227160],
                [55.983995, 37.227277],
                [55.984285, 37.227312],
                [55.984620, 37.227751],
                [55.984775, 37.228268],
                [55.984994, 37.228813],
                [55.985099, 37.229439],
                [55.985011, 37.231251],
                [55.985106, 37.237557],
                [55.987072, 37.234469],
                [55.988688, 37.235634],
                [55.988684, 37.233465],
                [55.989254, 37.233201],
                [55.989624, 37.232210],
                [55.989727, 37.230745],
                [55.989863, 37.228648],
                [55.990875, 37.227898],
                [55.991387, 37.226452],
                [55.992702, 37.225514],
                [55.991952, 37.224566],
                [55.991557, 37.224622],
                [55.991596, 37.221290],
                [55.992022, 37.217301],
                [55.992796, 37.215056],
                [55.993945, 37.215333],
                [55.994620, 37.215162],
                [55.995277, 37.215558],
                [55.995724, 37.217543],
                [55.996614, 37.218959],
                [55.997299, 37.218151],
                [55.997517, 37.216897],
                [55.998313, 37.217873],
                [55.999878, 37.217693],
                [56.003758, 37.218923],
                [56.005536, 37.219402],
                [56.005434, 37.225705],
                [56.006364, 37.226956],
                [56.012627, 37.210334]
            ]
        ];
        async function loadShopCoordinates() {
            try {
                const response = await fetch('/api/route-service?_=' + Date.now(), {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                // Проверка типа содержимого
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    throw new Error(`Invalid content type. Response: ${text.substring(0, 100)}`);
                }

                const data = await response.json();

                if (!Array.isArray(data)) {
                    throw new Error("Invalid data format");
                }

                return data.map(coord => {
                    const [lat, lng] = coord.split(',').map(Number);
                    return { lat, lng, original: coord };
                }).filter(coord => !isNaN(coord.lat) && !isNaN(coord.lng));

            } catch (error) {
                console.error('Load error:', error);
                showMessage('Временные проблемы с загрузкой данных');
                return getFallbackCoords(); // Возвращаем тестовые координаты
            }
        }

        function getFallbackCoords() {
            return [
                { lat: 55.998197, lng: 37.184308 },
                { lat: 55.970984, lng: 37.181232 },
                { lat: 55.985393, lng: 37.236123 }
            ];
        }

        function addZonesAndMarkers() {
            // Добавляем зоны
            zones.push(new ymaps.Polygon(PolygonCoords_1, {}, {fillColor: '#00640013', strokeColor: '#00640080', strokeWidth: 1.7}));
            zones.push(new ymaps.Polygon(PolygonCoords_2, {}, {fillColor: '#00640013', strokeColor: '#00640080', strokeWidth: 1.7}));
            zones.push(new ymaps.Polygon(PolygonCoords_3, {}, {fillColor: '#00640013', strokeColor: '#00640080', strokeWidth: 1.7}));
            zones.push(new ymaps.Polygon(PolygonCoords_peresecheniya, {}, {fillColor: '#FF450013', strokeColor: '#FF450080', strokeWidth: 1.7}));

            zones.forEach(zone => map.geoObjects.add(zone));

            // Загружаем и добавляем маркеры магазинов
            loadShopCoordinates().then(addresses => {
                if (addresses.length === 0) {
                    console.warn('No shop coordinates loaded');
                    return;
                }

                addresses.forEach(coord => {
                    // Создаем метку с встроенной оранжевой иконкой
                    const placemark = new ymaps.Placemark(
                        [coord.lat, coord.lng],
                        {
                            balloonContent: `Магазин: ${coord.original}`
                        },
                        {
                            // Используем стандартную оранжевую точку из набора иконок Яндекса
                            preset: 'islands#orangeDotIcon',
                            // Дополнительные параметры для лучшего отображения
                            iconColor: '#ff9900',  // Ярко-оранжевый цвет
                            balloonCloseButton: true,
                            hideIconOnBalloonOpen: false
                        }
                    );

                    // Добавляем метку в массив и на карту
                    markers.push(placemark);
                    map.geoObjects.add(placemark);

                    // Для отладки можно вывести в консоль информацию о созданной метке
                    console.log(`Создана метка магазина: ${coord.original}`, placemark);
                });
            });
        }

        function removeZonesAndMarkers() {
            zones.forEach(z => map.geoObjects.remove(z));
            zones = [];
            markers.forEach(m => map.geoObjects.remove(m));
            markers = [];
        }

        window.showZonesAndMarkers = function () {
            if (!zonesVisible) {
                addZonesAndMarkers();
            } else {
                removeZonesAndMarkers();
            }
            zonesVisible = !zonesVisible;
        }

        function drawRoute(startAddress, endAddress, routingMode) {
            if (route) {
                map.geoObjects.remove(route);
            }

            if (!zones || zones.length === 0) {
                showMessage("Для построения маршрута нажмите 'Отображение зон'");
                return;
            }

            ymaps.geocode(startAddress).then(function (resStart) {
                if (!resStart.geoObjects.getLength()) {
                    showMessage("Не удалось найти начальный адрес.");
                    return;
                }

                var startCoordinates = resStart.geoObjects.get(0).geometry.getCoordinates();

                ymaps.geocode(endAddress).then(function (resEnd) {
                    if (!resEnd.geoObjects.getLength()) {
                        showMessage("Не удалось найти конечный адрес.");
                        return;
                    }

                    var endCoordinates = resEnd.geoObjects.get(0).geometry.getCoordinates();

                    var isStartInsideZone = zones.some(zone => zone.geometry.contains(startCoordinates));
                    var isEndInsideZone = zones.some(zone => zone.geometry.contains(endCoordinates));

                    if (!isStartInsideZone || !isEndInsideZone) {
                        showMessage("Доставка осуществляется только в пределах Зеленограда!");
                        return;
                    }

                    // Создаем маршрут в зависимости от выбранного режима
                    route = new ymaps.multiRouter.MultiRoute({
                        referencePoints: [startCoordinates, endCoordinates],
                        params: {
                            routingMode: routingMode // 'auto' или 'pedestrian'
                        }
                    }, {
                        boundsAutoApply: true
                    });

                    route.model.events.add('requestsuccess', function () {
                        var routes = route.getRoutes();

                        if (routes.getLength() === 0) {
                            showMessage("Не удалось построить маршрут.");
                            return;
                        }

                        var minTime = Infinity;
                        var minDistance = Infinity;
                        var bestRoute = null;

                        routes.each(function (routeItem) {
                            var time = routeItem.properties.get("duration").value;
                            var distance = routeItem.properties.get("distance").value;

                            if (time < minTime) {
                                minTime = time;
                                minDistance = distance;
                                bestRoute = routeItem;
                            }
                        });

                        if (bestRoute) {
                            var timeText = bestRoute.properties.get("duration").text;
                            var distanceText = bestRoute.properties.get("distance").text;

                            var message = `🚗 Время в пути: ${timeText}<br>📍 Расстояние: ${distanceText}`;
                            showPopupMessage(message);
                        }
                        // Логирование маршрута на сервер
                        // В функции drawRoute замените блок логирования на:
                        try {
                            fetch('/api/log-service', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    message: `Route from ${startAddress} to ${endAddress} (${routingMode})`
                                })
                            }).catch(err => {
                                console.error("Ошибка логирования маршрута:", err);
                            });
                        } catch (e) {
                            console.error("Ошибка в fetch логировании маршрута:", e);
                        }

                    });


                    route.model.events.add('requesterror', function (err) {
                        console.error("Ошибка при построении маршрута:", err);
                        showMessage("Ошибка при построении маршрута.");
                    });

                    map.geoObjects.add(route);
                }).catch(err => {
                    console.error("Ошибка геокодирования конечного адреса:", err);
                    showMessage("Ошибка при обработке конечного адреса.");
                });
            }).catch(err => {
                console.error("Ошибка геокодирования начального адреса:", err);
                showMessage("Ошибка при обработке начального адреса.");
            });
        }


        function findNearestPoint(userAddress, routingMode) {
            fetch('/api/route-service')
                .then(res => res.json())
                .then(async addresses => {
                    let userCoords = await ymaps.geocode(userAddress).then(r => r.geoObjects.get(0).geometry.getCoordinates());

                    let nearest = null, minDist = Infinity;

                    for (const address of addresses) {
                        let coords = await ymaps.geocode(address).then(r => r.geoObjects.get(0).geometry.getCoordinates());
                        let dist = ymaps.coordSystem.geo.getDistance(userCoords, coords);
                        if (dist < minDist) {
                            minDist = dist;
                            nearest = address;
                        }
                    }

                    drawRoute(userAddress, nearest, routingMode);
                });
        }

        document.getElementById('findRouteButton').onclick = function () {
            let address = document.getElementById('addressInput').value.trim();
            if (!address) return showMessage("Введите адрес!");
            let mode = document.getElementById('routeType').value;
            findNearestPoint(address, mode);
        }

        document.getElementById('autoButton').onclick = showZonesAndMarkers;

        function showMessage(text) {
            let box = document.getElementById("messageBox");
            box.textContent = text;
            box.style.display = "block";
            setTimeout(() => box.style.display = "none", 3000);
        }

        function showPopupMessage(message) {
            let popup = document.createElement("div");
            popup.innerHTML = `<p style="margin: 0;">${message}</p>`;
            Object.assign(popup.style, {
                position: 'fixed',
                top: '20px',
                right: '20px',
                padding: '9px 10px',
                background: 'rgba(50, 50, 50, 0.9)',
                color: '#fff',
                borderRadius: '10px',
                boxShadow: '0 4px 10px rgba(0,0,0,0.3)',
                fontFamily: "'Inter', sans-serif",
                fontSize: '12px',
                zIndex: 1000
            });
            document.body.appendChild(popup);
            setTimeout(() => popup.remove(), 4000);
        }
    }

</script>
</body>
</html>
