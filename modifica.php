<?php

require_once 'config.php';

$canzone_dettagli = null; 
$error_message = ''; 

if (isset($_GET['id'])) {
    $id_canzone = (int) $_GET['id'];

    if ($id_canzone > 0) {

        $sql = "SELECT id, titolo, artista, link FROM canzoni WHERE id = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id_canzone);

            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    $canzone_dettagli = mysqli_fetch_assoc($result);
                } else {
                    $error_message = 'Canzone non trovata.';
                }
                mysqli_free_result($result);
            } else {
                $error_message = 'Errore nell\'esecuzione della query: ' . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = 'Errore nella preparazione della query: ' . mysqli_error($conn);
        }
    } else {
        $error_message = 'ID canzone non valido. Torna indietro e seleziona una canzone valida.';
    }
} else {
    $error_message = 'Nessun ID canzone specificato. Torna indietro e seleziona una canzone.';
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $canzone_dettagli ? htmlspecialchars($canzone_dettagli['titolo']) : 'Dettagli Canzone'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #1a1a1a;
            --text-color: #f0f0f0;
            --accent-color: #ff0055;
            --light-text-color: #aaaaaa;
            --border-color: #333333;
            --container-bg: #282828;
            --highlight-color: rgba(0, 255, 255, 0.4);
            --info-bg: #3a3a3a;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center;
            justify-content: flex-start;
            overflow-x: hidden;
            padding: 20px;
            box-sizing: border-box;
        }

        .container {
            background-color: var(--container-bg);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            max-width: 900px;
            width: 100%;
            padding: 30px;
            text-align: center;
            border: 1px solid var(--border-color);
            margin-top: 50px;
        }

        h1 {
            color: var(--accent-color);
            font-size: 2.8em;
            letter-spacing: -0.05em;
            font-weight: 700;
            margin-bottom: 10px;
        }

        h2 {
            color: var(--text-color);
            font-size: 1.8em;
            margin-top: 0;
            margin-bottom: 30px;
        }

        p {
            color: var(--light-text-color);
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .error-message {
            color: var(--accent-color);
            font-size: 1.3em;
            margin-top: 30px;
        }

        .back-button {
            display: inline-block;
            margin-top: 40px;
            padding: 12px 25px;
            background-color: var(--accent-color);
            color: var(--text-color);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .back-button:hover {
            background-color: #e6004c;
            transform: translateY(-2px);
        }

        .audio-player-container {
            margin: 30px 0;
            width: 100%;
        }

        audio {
            width: 100%;
            height: 50px;
            background-color: var(--border-color);
            border-radius: 25px;
            outline: none;
            filter: invert(1) hue-rotate(180deg);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .joystick-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
            position: relative;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .joystick-section h3 {
            color: var(--text-color);
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        #hotspot-description-display {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--info-bg);
            color: var(--text-color);
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 90%;
            z-index: 20;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }


        .joystick-wrapper {
            position: relative;
            width: 80%;
            max-width: 500px;
            height: 0;
            padding-bottom: 56.25%;
            margin: 0 auto;
            filter: drop-shadow(0 0 15px rgba(0, 255, 255, 0.3));
            overflow: hidden;
        }

        .joystick-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .hotspot {
            position: absolute;
            background-color: transparent;
            cursor: pointer;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
            z-index: 10;
        }

        .hotspot.highlight {
            background-color: var(--highlight-color);
            box-shadow: 0 0 15px var(--accent-color);
        }

        #right-stick {
            left: 59%;
            top: 50%;
            width: 10%;
            height: 13%;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        #left-stick {
            left: 41%;
            top: 50%;
            width: 12%;
            height: 13%;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        #l2-trigger {
            left: 32%;
            top: 31%;
            width: 11%;
            height: 6%;
            border-radius: 10% / 50%;
            transform: translate(-50%, -50%) rotate(-10deg);
        }

        #r2-trigger {
            left: 69%;
            top: 32%;
            width: 10%;
            height: 6%;
            border-radius: 10% / 50%;
            transform: translate(-50%, -50%) rotate(10deg);
        }

        #l1-button {
            left: 32%;
            top: 25%;
            width: 9%;
            height: 4%;
            border-radius: 10% / 50%;
            transform: translate(-50%, -50%) rotate(-10deg);
        }

        #r1-button {
            left: 69%;
            top: 26%;
            width: 9%;
            height: 4%;
            border-radius: 10% / 50%;
            transform: translate(-50%, -50%) rotate(10deg);
        }

        #button-triangle {
            top: 37%;
            left: 68%;
            width: 4%;
            height: 6%;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        #button-circle {
            top: 41%;
            left: 72%;
            width: 5%;
            height: 6%;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        #button-x {
            top: 45%;
            left: 68%;
            width: 5%;
            height: 6%;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        #button-square {
            top: 41%;
            left: 64%;
            width: 5%;
            height: 6%;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        #dpad-up {
            top: 39%;
            left: 33%;
            width: 5%;
            height: 5%;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        #dpad-down {
            top: 45%;
            left: 33%;
            width: 5%;
            height: 6%;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        #dpad-left {
            top: 42%;
            left: 29%;
            width: 5%;
            height: 6%;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        #dpad-right {
            top: 42%;
            left: 36%;
            width: 5%;
            height: 5%;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        #touchpad {
            top: 39%;
            left: 50%;
            width: 20%;
            height: 11%;
            border-radius: 24px;
            transform: translate(-50%, -50%);
        }

        #ps-button {
            top: 50%;
            left: 50%;
            width: 6%;
            height: 8%;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        #share-button {
            top: 36%;
            left: 39%;
            width: 4%;
            height: 5%;
            border-radius: 5px;
            transform: translate(-50%, -50%);
        }

        #options-button {
            top: 35%;
            left: 62%;
            width: 4%;
            height: 5%;
            border-radius: 5px;
            transform: translate(-50%, -50%);
        }


        #function-display {
            margin-top: 20px;
            font-size: 1.5em;
            font-weight: bold;
            color: var(--accent-color);
            min-height: 1.5em;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
        }

        #gamepad-info {
            background-color: var(--info-bg);
            color: var(--light-text-color);
            padding: 15px 20px;
            border-radius: 10px;
            margin-top: 20px;
            width: 80%;
            max-width: 500px;
            font-size: 0.9em;
            text-align: left;
            border: 1px solid var(--border-color);
        }

        #gamepad-info strong {
            color: var(--text-color);
        }

        #gamepad-status {
            color: var(--accent-color);
            font-weight: bold;
        }

        #gamepad-axes,
        #gamepad-buttons {
            background-color: #1a1a1a;
            border-radius: 5px;
            padding: 8px;
            margin-top: 5px;
            font-family: 'Consolas', 'Monaco', monospace;
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 100px;
            overflow-y: auto;
        }


        @media (max-width: 768px) {
            h1 {
                font-size: 2em;
            }

            h2 {
                font-size: 1.5em;
            }

            .container {
                padding: 20px;
            }

            .joystick-wrapper {
                width: 95%;
            }

            #function-display {
                font-size: 1.2em;
            }

            #gamepad-info {
                width: 95%;
            }

            #hotspot-description-display {
                font-size: 1em;
                top: -5px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.8em;
            }

            h2 {
                font-size: 1.2em;
            }

            .joystick-wrapper {
                width: 100%;
            }

            #function-display {
                font-size: 1em;
            }

            #hotspot-description-display {
                font-size: 0.9em;
                top: 0px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
            <a href="index.php" class="back-button">Torna alla playlist</a>
        <?php elseif ($canzone_dettagli): ?>
            <h1><?php echo htmlspecialchars($canzone_dettagli['titolo']); ?></h1>
            <?php if (!empty($canzone_dettagli['artista'])): ?>
                <h2>di <?php echo htmlspecialchars($canzone_dettagli['artista']); ?></h2>
            <?php endif; ?>

            <p>Stai manipolando l'audio di "<?php echo htmlspecialchars($canzone_dettagli['titolo']); ?>".</p>

            <div class="audio-player-container">
                <?php
                $audio_extensions = ['.mp3', '.wav', '.ogg'];
                $is_audio_file = false;
                foreach ($audio_extensions as $ext) {
                    if (str_ends_with(strtolower($canzone_dettagli['link']), $ext)) {
                        $is_audio_file = true;
                        break;
                    }
                }

                if ($is_audio_file) {
                    echo '<audio controls loop id="audioPlayer">';
                    echo '<source src="' . htmlspecialchars($canzone_dettagli['link']) . '" type="audio/mpeg">';
                    echo 'Il tuo browser non supporta l\'elemento audio.';
                    echo '</audio>';
                    echo '<button id="startAudioButton" style="margin-top: 15px; padding: 10px 20px; font-size: 1.1em; background-color: var(--accent-color); color: var(--text-color); border: none; border-radius: 8px; cursor: pointer; transition: background-color 0.2s ease;">Avvia Audio e Controlli Gamepad</button>';
                } else {
                    echo '<p>Il link fornito non è un file audio riproducibile direttamente: <a href="' . htmlspecialchars($canzone_dettagli['link']) . '" target="_blank" style="color: var(--accent-color); text-decoration: underline;">' . htmlspecialchars($canzone_dettagli['link']) . '</a></p>';
                    echo '<p class="error-message">Per la riproduzione automatica, assicurati che il link nel database punti a un file .mp3, .wav o .ogg.</p>';
                }
                ?>
            </div>

            <div class="joystick-section">
                <h3>Controller Esterno Attivo</h3>
                <p>Usa il tuo gamepad fisico per manipolare l'audio.</p>

                <div id="hotspot-description-display"></div>

                <div class="joystick-wrapper">
                    <img src="imgjoystick.webp" alt="PlayStation 4 Controller" class="joystick-image" id="ps4-joystick">

                    <div class="hotspot" id="left-stick" data-function="Stick Sinistro: Panning (X), Volume (Y)"></div>
                    <div class="hotspot" id="right-stick" data-function="Stick Destro: Pitch (X)"></div>
                    <div class="hotspot" id="l2-trigger" data-function="L2 Trigger: Filtro Lowpass (Frequenza)"></div>
                    <div class="hotspot" id="r2-trigger" data-function="R2 Trigger: Delay (Feedback)"></div>
                    <div class="hotspot" id="l1-button" data-function="L1 Button: Placeholder L1"></div>
                    <div class="hotspot" id="r1-button" data-function="R1 Button: Placeholder R1"></div>

                    <div class="hotspot" id="button-triangle" data-function="Triangolo: Distorsione On/Off"></div>
                    <div class="hotspot" id="button-circle" data-function="Cerchio: Chorus On/Off"></div>
                    <div class="hotspot" id="button-x" data-function="X: Distorsione On/Off"></div>
                    <div class="hotspot" id="button-square" data-function="Quadrato: Bitcrusher On/Off"></div>

                    <div class="hotspot" id="dpad-up" data-function="Su: Aumento Ritardo + Feedback"></div>
                    <div class="hotspot" id="dpad-down" data-function="D-Pad Giù: Diminuisco Ritardo + Feedback"></div>
                    <div class="hotspot" id="dpad-left" data-function="D-Pad Sinistra: Aumento Mix"></div>
                    <div class="hotspot" id="dpad-right" data-function="D-Pad Destra: Diminuisco Mix"></div>

                    <div class="hotspot" id="touchpad" data-function="Touchpad: Scrubbing Audio / Pan"></div>
                    <div class="hotspot" id="ps-button" data-function="PS Button: Mute / Reset Effetti"></div>
                    <div class="hotspot" id="share-button" data-function="Share Button: Salva Stato Attuale"></div>
                    <div class="hotspot" id="options-button" data-function="Options Button: Impostazioni Avanzate"></div>
                </div>

                <div id="function-display">Pronto...</div>
            </div>

            <div id="gamepad-info">
                <p>Stato Gamepad: <span id="gamepad-status">Disconnesso</span></p>
                <p>Nome: <span id="gamepad-name">N/A</span></p>
                <p>Assi: <span id="gamepad-axes">N/A</span></p>
                <p>Pulsanti: <span id="gamepad-buttons">N/A</span></p>
                <p class="small-text">Premi un tasto o muovi uno stick sul tuo gamepad per attivarlo.</p>
            </div>

            <a href="index.php" class="back-button">Torna alla Playlist</a>

        <?php else: ?>
            <p class="error-message">Errore sconosciuto. Nessuna canzone da mostrare.</p>
            <a href="index.php" class="back-button">Torna alla Playlist</a>
        <?php endif; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const audioElement = document.getElementById('audioPlayer');
            const startAudioButton = document.getElementById('startAudioButton');
            const gamepadStatusSpan = document.getElementById('gamepad-status');
            const gamepadNameSpan = document.getElementById('gamepad-name');
            const gamepadAxesPre = document.getElementById('gamepad-axes');
            const gamepadButtonsPre = document.getElementById('gamepad-buttons');
            const functionDisplay = document.getElementById('function-display');
            const hotspotDescriptionDisplay = document.getElementById('hotspot-description-display');
            const hotspots = document.querySelectorAll('.hotspot');

            let audioContext = null;
            let source = null;
            let masterGainNode = null; 
            let pannerNode = null; 
            let distortionNode = null; 
            let isDistortionOn = false;
            let pitchFilterNode = null; 
            let pitchGainNode = null;  
            let p1Node;
            let p1GainNode;
            let tapeStopInterval;
            const tapeStopDuration = 300; 
            const tapeStopSteps = 30; 
            let tapeStopCurrentStep = 0;
            let bitCrusherNode = null;
            let isBitCrusherOn = false;
            let p2_was_pressed = false;
            let delayNode = null;     
            let feedbackGainNode = null; 
            let wetGainNode = null;    
            let dryGainNode = null;    

            let connectedGamepad = null; 
            let gamepadLoop = null; 
        

            const sensitivityThreshold = 0.1; 
            let p0_was_pressed = false; 
            let p12_was_pressed = false;
            let p13_was_pressed = false;
            let p14_was_pressed = false;
            let p15_was_pressed = false;

            let currentDelayTime = 0.0; 
            let currentFeedback = 0.0;
            let currentWetMix = 0.0;

            const delayStep = 0.1;       
            const feedbackStep = 0.05;  
            const mixStep = 0.1;        
            const maxDelay = 2.0;      
            const minDelay = 0.05;      
            const maxFeedback = 0.9;    
            const minFeedback = 0.0;    

            
            function makeDistortionCurve(amount) { 
                const k = typeof amount === 'number' ? amount : 50;
                const n_samples = 44100; 
                const curve = new Float32Array(n_samples); 
                const deg = Math.PI / 180;

                
                for (let i = 0; i < n_samples; ++i) {
                    const x = i * 2 / n_samples - 1;
                    curve[i] = (3 + k) * x * 20 * deg / (Math.PI + k * Math.abs(x));
                }
                return curve;
            }

            function setupDistortionNode() {
                if (!audioContext) {
                    console.error("AudioContext non disponibile per settare la distorsione.");
                    return;
                }
                distortionNode = audioContext.createWaveShaper();
                distortionNode.curve = makeDistortionCurve(400);
                distortionNode.oversample = '4x';
                console.log("Nodo Distorsione inizializzato.");
            }

            function toggleDistortion() {
                if (!source || !masterGainNode || !pannerNode || !distortionNode || !pitchFilterNode || !pitchGainNode) {
                    console.warn("Nodi audio non completamente inizializzati per la distorsione.");
                    return;
                }

                
                source.disconnect();
                distortionNode.disconnect();
                masterGainNode.disconnect();
                pitchFilterNode.disconnect(); 

                if (isDistortionOn) {
                    source.connect(masterGainNode); 
                    setGamepadFunctionText('Distorsione: SPENTA');
                    console.log('Distorsione disattivata.');
                } else { 
                    source.connect(distortionNode); 
                    distortionNode.connect(masterGainNode); 
                    setGamepadFunctionText('Distorsione: ACCESA!');
                    console.log('Distorsione attivata.');
                }

                masterGainNode.connect(pitchFilterNode);
                pitchFilterNode.connect(pitchGainNode); 
                pitchGainNode.connect(dryGainNode);
                dryGainNode.connect(pannerNode);
                pitchGainNode.connect(delayNode);
                delayNode.connect(feedbackGainNode);
                feedbackGainNode.connect(delayNode);
                delayNode.connect(wetGainNode);
                wetGainNode.connect(pannerNode);
                pannerNode.connect(audioContext.destination);

                isDistortionOn = !isDistortionOn; 
            }

            
            function setupBitCrusherNode() {
                if (!audioContext) {
                    console.error("AudioContext non disponibile per settare il Bit Crusher.");
                    return;
                }
                try {
                    audioContext.audioWorklet.addModule('bit-crusher-processor.js').then(() => {
                        bitCrusherNode = new AudioWorkletNode(audioContext, 'bit-crusher-processor');
                        bitCrusherNode.parameters.get('bitDepth').value = 4; 
                        bitCrusherNode.parameters.get('normFreq').value = 0.2; 
                        console.log("Nodo Bit Crusher (AudioWorklet) inizializzato con effetto accentuato.");
                    }).catch(e => {
                        console.warn("Impossibile caricare AudioWorklet per Bit Crusher, usando fallback ScriptProcessorNode:", e);
                        bitCrusherNode = audioContext.createScriptProcessor(4096, 1, 1);
                        bitCrusherNode.onaudioprocess = function (event) {
                            const inputBuffer = event.inputBuffer.getChannelData(0);
                            const outputBuffer = event.outputBuffer.getChannelData(0);

                            
                            const bitDepth = 4; 
                            const sampleRateDivisor = 5; 

                            const step = Math.pow(0.5, bitDepth); 
                            for (let i = 0; i < inputBuffer.length; i++) {
                                outputBuffer[i] = Math.floor(inputBuffer[i] / step) * step;

                                if (i % sampleRateDivisor !== 0) {
                                    outputBuffer[i] = outputBuffer[i - (i % sampleRateDivisor)];
                                }
                            }
                        };
                        console.log("Nodo Bit Crusher (ScriptProcessorNode) inizializzato con effetto accentuato come fallback.");
                    });
                } catch (e) {
                    console.error("Errore durante l'inizializzazione dell'AudioWorklet:", e);
                    bitCrusherNode = audioContext.createScriptProcessor(4096, 1, 1);
                    bitCrusherNode.onaudioprocess = function (event) {
                        const inputBuffer = event.inputBuffer.getChannelData(0);
                        const outputBuffer = event.outputBuffer.getChannelData(0);

                        const bitDepth = 4;
                        const sampleRateDivisor = 5;

                        const step = Math.pow(0.5, bitDepth);
                        for (let i = 0; i < inputBuffer.length; i++) {
                            outputBuffer[i] = Math.floor(inputBuffer[i] / step) * step;
                            if (i % sampleRateDivisor !== 0) {
                                outputBuffer[i] = outputBuffer[i - (i % sampleRateDivisor)];
                            }
                        }
                    };
                    console.log("Nodo Bit Crusher (ScriptProcessorNode) inizializzato con effetto accentuato come fallback definitivo.");
                }
            }


            function toggleBitCrusher() {
                if (!source || !masterGainNode || !pannerNode || !bitCrusherNode || !pitchFilterNode || !pitchGainNode) {
                    console.warn("Nodi audio non completamente inizializzati per il Bit Crusher.");
                    return;
                }

                source.disconnect();
                distortionNode.disconnect(); 
                bitCrusherNode.disconnect(); 
                masterGainNode.disconnect();
                pitchFilterNode.disconnect(); 
                if (isDistortionOn) { 
                    source.connect(distortionNode);
                    if (isBitCrusherOn) { 
                        distortionNode.connect(masterGainNode);
                        setGamepadFunctionText('Bit Crusher: SPENTO');
                        console.log('Bit Crusher disattivato.');
                    } else { 
                        distortionNode.connect(bitCrusherNode);
                        bitCrusherNode.connect(masterGainNode);
                        setGamepadFunctionText('Bit Crusher: ACCESO!');
                        console.log('Bit Crusher attivato.');
                    }
                } else { 
                    if (isBitCrusherOn) { 
                        source.connect(masterGainNode);
                        setGamepadFunctionText('Bit Crusher: SPENTO');
                        console.log('Bit Crusher disattivato.');
                    } else { 
                        source.connect(bitCrusherNode);
                        bitCrusherNode.connect(masterGainNode);
                        setGamepadFunctionText('Bit Crusher: ACCESO!');
                        console.log('Bit Crusher attivato.');
                    }
                }

                masterGainNode.connect(pitchFilterNode);
                pitchFilterNode.connect(pitchGainNode);
                pitchGainNode.connect(dryGainNode);
                dryGainNode.connect(pannerNode);
                pitchGainNode.connect(delayNode);
                delayNode.connect(feedbackGainNode);
                feedbackGainNode.connect(delayNode);
                delayNode.connect(wetGainNode);
                wetGainNode.connect(pannerNode);
                pannerNode.connect(audioContext.destination);

                isBitCrusherOn = !isBitCrusherOn; 
            }


            function initAudioContext() { 
                if (audioContext) return; 
                console.log("Inizializzazione AudioContext...");
                audioContext = new (window.AudioContext || window.webkitAudioContext)();

                setupDistortionNode();
                setupBitCrusherNode();

                source = audioContext.createMediaElementSource(audioElement);
                masterGainNode = audioContext.createGain(); 
                pannerNode = audioContext.createStereoPanner(); 

                pitchFilterNode = audioContext.createBiquadFilter(); 
                pitchFilterNode.type = 'lowpass'; 
                pitchFilterNode.frequency.value = 22000; 
                pitchFilterNode.Q.value = 10; 
                console.log("Nodo Pitch Filter inizializzato con Q aumentato.");
                pitchGainNode = audioContext.createGain(); 
                pitchGainNode.gain.value = 1; 
                console.log("Nodo Pitch Gain inizializzato.");

                delayNode = audioContext.createDelay(maxDelay); 
                delayNode.delayTime.value = currentDelayTime; 

                feedbackGainNode = audioContext.createGain(); 
                feedbackGainNode.gain.value = currentFeedback; 
                console.log("Feedback Gain Node inizializzato.");

                wetGainNode = audioContext.createGain();
                wetGainNode.gain.value = currentWetMix; 
                console.log("Wet Gain Node inizializzato.");

                dryGainNode = audioContext.createGain();
                dryGainNode.gain.value = 1.0 - currentWetMix; 
                console.log("Dry Gain Node inizializzato.");

                
                source.connect(masterGainNode); 
                masterGainNode.connect(pitchFilterNode); 
                pitchFilterNode.connect(pitchGainNode); 

                pitchGainNode.connect(dryGainNode); 
                dryGainNode.connect(pannerNode); 
                pitchGainNode.connect(delayNode);      
                delayNode.connect(feedbackGainNode);   
                feedbackGainNode.connect(delayNode);   
                delayNode.connect(wetGainNode);        
                wetGainNode.connect(pannerNode);       

                pannerNode.connect(audioContext.destination); 

                console.log("AudioContext inizializzato e catena audio creata (Volume, Panning, Pitch, Eco, Distortion DISATTIVATA, Bit Crusher DISATTIVATO).");
            }

            window.addEventListener('gamepadconnected', (event) => {
                console.log('Gamepad connesso:', event.gamepad);
                connectedGamepad = event.gamepad;
                updateGamepadDisplayRawData();
                startGamepadLoop();
                gamepadStatusSpan.textContent = 'Connesso';
                gamepadStatusSpan.className = 'status-connected';
                functionDisplay.textContent = 'Gamepad Connesso!';
            });

            window.addEventListener('gamepaddisconnected', (event) => {
                console.log('Gamepad disconnesso:', event.gamepad);
                connectedGamepad = null;
                stopGamepadLoop();
                gamepadStatusSpan.textContent = 'Disconnesso';
                gamepadStatusSpan.className = 'status-disconnected';
                gamepadNameSpan.textContent = 'N/A';
                gamepadAxesPre.textContent = 'Attendere connessione gamepad...';
                gamepadButtonsPre.textContent = 'Attendere connessione gamepad...';
                functionDisplay.textContent = 'Gamepad Disconnesso.';
            });

            function startGamepadLoop() {
                if (!gamepadLoop) {
                    gamepadLoop = requestAnimationFrame(gamepadUpdateLoop);
                    console.log('Loop di aggiornamento Gamepad avviato.');
                }
            }

            function stopGamepadLoop() {
                if (gamepadLoop) {
                    cancelAnimationFrame(gamepadLoop);
                    gamepadLoop = null;
                    console.log('Loop di aggiornamento Gamepad interrotto.');
                }
            }

            function gamepadUpdateLoop() {
                const gamepads = navigator.getGamepads();
                connectedGamepad = gamepads[0];

                if (connectedGamepad) {
                    updateGamepadDisplayRawData();
                    updateAudioEffects(connectedGamepad); 

                    const p0Button = connectedGamepad.buttons[0]; 
                    if (p0Button && p0Button.pressed && !p0_was_pressed) {
                        toggleDistortion();
                    }
                    p0_was_pressed = p0Button ? p0Button.pressed : false;

                    const p2Button = connectedGamepad.buttons[2]; 
                    if (p2Button && p2Button.pressed && !p2_was_pressed) {
                        toggleBitCrusher();
                    }
                    p2_was_pressed = p2Button ? p2Button.pressed : false;

                    handleEcoButtons(connectedGamepad);

                    if (audioContext && audioContext.state === 'suspended') {
                        audioContext.resume().catch(e => console.error("Errore nel ripristino dell'AudioContext:", e));
                    }
                    
                } else {
                    stopGamepadLoop();
                    gamepadStatusSpan.textContent = 'Disconnesso';
                    gamepadStatusSpan.className = 'status-disconnected';
                    gamepadNameSpan.textContent = 'N/A';
                    gamepadAxesPre.textContent = 'Attendere connessione gamepad...';
                    gamepadButtonsPre.textContent = 'Attendere connessione gamepad...';
                    functionDisplay.textContent = 'Gamepad Disconnesso.';
                }
                gamepadLoop = requestAnimationFrame(gamepadUpdateLoop);
            }

            function updateGamepadDisplayRawData() {
                if (!connectedGamepad) return;
                gamepadNameSpan.textContent = connectedGamepad.id;
                const axesValues = connectedGamepad.axes.map(axis => axis.toFixed(3));
                gamepadAxesPre.textContent = `[${axesValues.join(', ')}]`;
                const buttonStates = connectedGamepad.buttons.map((button, index) => {
                    if (button.pressed) {
                        return `P${index}: ${button.value.toFixed(3)} (Premuto)`;
                    } else {
                        return `P${index}: ${button.value.toFixed(3)}`;
                    }
                });
                gamepadButtonsPre.textContent = buttonStates.join('\n');
            }

            let gamepadDisplayTimeout = null;
            function setGamepadFunctionText(text) {
                functionDisplay.textContent = text;
                if (gamepadDisplayTimeout) {
                    clearTimeout(gamepadDisplayTimeout);
                }
                gamepadDisplayTimeout = setTimeout(() => {
                    const gamepads = navigator.getGamepads();
                    const currentGp = gamepads[0];
                    const anyStickActivity = (currentGp && (
                        Math.abs(currentGp.axes[0]) > sensitivityThreshold || 
                        Math.abs(currentGp.axes[1]) > sensitivityThreshold || 
                        Math.abs(currentGp.axes[2]) > sensitivityThreshold     
                    ));

                    const anyButtonEcoActivity = (currentGp && (
                        currentGp.buttons[12] && currentGp.buttons[12].pressed ||
                        currentGp.buttons[13] && currentGp.buttons[13].pressed ||
                        currentGp.buttons[14] && currentGp.buttons[14].pressed ||
                        currentGp.buttons[15] && currentGp.buttons[15].pressed
                    ));

                    const anyButtonEffectActivity = (currentGp && (
                        currentGp.buttons[0] && currentGp.buttons[0].pressed || 
                        currentGp.buttons[2] && currentGp.buttons[2].pressed    
                    ));


                    if (anyStickActivity || anyButtonEcoActivity || anyButtonEffectActivity) {
                        setGamepadFunctionText(functionDisplay.textContent);
                    } else {
                        functionDisplay.textContent = 'Pronto...';
                    }
                }, 1000); 
            }


            function handleEcoButtons(gp) {
                if (!delayNode || !feedbackGainNode || !wetGainNode || !dryGainNode) return;

                const p12Button = gp.buttons[12];
                if (p12Button && p12Button.pressed && !p12_was_pressed) {
                    currentDelayTime = Math.min(maxDelay, currentDelayTime + delayStep);
                    currentFeedback = Math.min(maxFeedback, currentFeedback + feedbackStep);
                    updateEcoNodes();
                    setGamepadFunctionText(`Eco: Ritardo=${currentDelayTime.toFixed(2)}s, Feedback=${currentFeedback.toFixed(2)}, Mix=${currentWetMix.toFixed(2)}`);
                }
                p12_was_pressed = p12Button ? p12Button.pressed : false;

                const p13Button = gp.buttons[13];
                if (p13Button && p13Button.pressed && !p13_was_pressed) {
                    currentDelayTime = Math.max(minDelay, currentDelayTime - delayStep);
                    currentFeedback = Math.max(minFeedback, currentFeedback - feedbackStep);
                    updateEcoNodes();
                    setGamepadFunctionText(`Eco: Ritardo=${currentDelayTime.toFixed(2)}s, Feedback=${currentFeedback.toFixed(2)}, Mix=${currentWetMix.toFixed(2)}`);
                }
                p13_was_pressed = p13Button ? p13Button.pressed : false;

                const p14Button = gp.buttons[14];
                if (p14Button && p14Button.pressed && !p14_was_pressed) {
                    currentWetMix = Math.min(1.0, currentWetMix + mixStep);
                    updateEcoNodes();
                    setGamepadFunctionText(`Eco: Ritardo=${currentDelayTime.toFixed(2)}s, Feedback=${currentFeedback.toFixed(2)}, Mix=${currentWetMix.toFixed(2)}`);
                }
                p14_was_pressed = p14Button ? p14Button.pressed : false;

                const p15Button = gp.buttons[15];
                if (p15Button && p15Button.pressed && !p15_was_pressed) {
                    currentWetMix = Math.max(0.0, currentWetMix - mixStep);
                    updateEcoNodes();
                    setGamepadFunctionText(`Eco: Ritardo=${currentDelayTime.toFixed(2)}s, Feedback=${currentFeedback.toFixed(2)}, Mix=${currentWetMix.toFixed(2)}`);
                }
                p15_was_pressed = p15Button ? p15Button.pressed : false;

                if (!p12_was_pressed && !p13_was_pressed && !p14_was_pressed && !p15_was_pressed &&
                    currentDelayTime === 0.0 && currentFeedback === 0.0 && currentWetMix === 0.0 &&
                    functionDisplay.textContent.startsWith('Eco:')) {
                    setGamepadFunctionText('Eco: Off');
                }
            }

            function updateEcoNodes() {
                if (delayNode) delayNode.delayTime.value = currentDelayTime;
                if (feedbackGainNode) feedbackGainNode.gain.value = currentFeedback;
                if (wetGainNode) wetGainNode.gain.value = currentWetMix;
                if (dryGainNode) dryGainNode.gain.value = 1.0 - currentWetMix; // Dry è l'opposto del wet
            }


            function updateAudioEffects(gp) {
                if (!audioContext || audioContext.state === 'suspended') {
                    console.warn("AudioContext non attivo o sospeso, non aggiorno effetti.");
                    return;
                }

                let gamepadActivityDetected = false;

                const leftStickY = gp.axes[1];
                if (masterGainNode) {
                    if (Math.abs(leftStickY) > sensitivityThreshold) {
                        masterGainNode.gain.value = Math.max(0, 1 - (leftStickY + 1) / 2);
                        const volumePercent = (masterGainNode.gain.value * 100).toFixed(0);
                        setGamepadFunctionText(`Volume: ${volumePercent}%`);
                        gamepadActivityDetected = true;
                    } else {
                        if (masterGainNode.gain.value !== 0.5) {
                            masterGainNode.gain.value = 0.5;
                        }
                    }
                }

                const leftStickX = gp.axes[0];
                if (pannerNode) {
                    if (Math.abs(leftStickX) > sensitivityThreshold) {
                        pannerNode.pan.value = leftStickX;
                        setGamepadFunctionText(`Panning: ${leftStickX.toFixed(2)}`);
                        gamepadActivityDetected = true;
                    } else {
                        if (pannerNode.pan.value !== 0) {
                            pannerNode.pan.value = 0;
                        }
                    }
                }

              
                const rightStickX = gp.axes[2];
                if (pitchFilterNode) {
                    if (Math.abs(rightStickX) > sensitivityThreshold) {
                        const minFreq = 100;
                        const maxFreq = audioContext.sampleRate / 2;
                        const normalizedValue = (rightStickX + 1) / 2;
                        const newFrequency = minFreq * Math.pow((maxFreq / minFreq), normalizedValue);

                        pitchFilterNode.frequency.cancelScheduledValues(audioContext.currentTime);
                        pitchFilterNode.frequency.value = newFrequency;

                        setGamepadFunctionText(`Pitch/Frequenza: ${newFrequency.toFixed(0)} Hz`);
                        gamepadActivityDetected = true;
                    } else {
                        if (pitchFilterNode.frequency.value !== 22000) {
                            pitchFilterNode.frequency.cancelScheduledValues(audioContext.currentTime); 
                            pitchFilterNode.frequency.value = 22000;
                        }
                    }
                }

                
                if (!gamepadActivityDetected && !functionDisplay.textContent.startsWith('Eco:') &&
                    !functionDisplay.textContent.startsWith('Distorsione:') &&
                    !functionDisplay.textContent.startsWith('Bit Crusher:')) {
                }
            }


            if (startAudioButton) {
                startAudioButton.addEventListener('click', () => {
                    console.log("Pulsante 'Avvia Audio' cliccato.");
                    initAudioContext();
                    audioElement.play().then(() => {
                        console.log("Audio avviato con successo tramite click.");
                        startAudioButton.style.display = 'none';
                        if (!gamepadLoop) {
                            startGamepadLoop();
                            setGamepadFunctionText('Audio Avviato. Muovi lo stick sinistro!');
                            setTimeout(() => {
                                if (functionDisplay.textContent === 'Audio Avviato. Muovi lo stick sinistro!') {
                                    functionDisplay.textContent = 'Pronto...';
                                }
                            }, 3000);
                        }
                    }).catch(e => {
                        console.error("Errore nell'avvio dell'audio tramite play():", e);
                        functionDisplay.textContent = "Errore: Permetti l'audio nel browser per avviare i controlli.";
                    });
                }, { once: true });
            } else {
                console.warn("Elemento #startAudioButton non trovato. L'audio potrebbe non avviarsi correttamente.");
            }


            hotspots.forEach(hotspot => {
                hotspot.addEventListener('mouseover', () => {
                    hotspot.classList.add('highlight');
                    const functionText = hotspot.dataset.function;
                    if (functionText) {
                        hotspotDescriptionDisplay.textContent = functionText;
                        hotspotDescriptionDisplay.style.opacity = '1';
                    }
                });

                hotspot.addEventListener('mouseout', () => {
                    hotspot.classList.remove('highlight');
                    hotspotDescriptionDisplay.style.opacity = '0';
                    hotspotDescriptionDisplay.textContent = '';
                });
            });


            const initialGamepadsCheck = navigator.getGamepads();
            if (initialGamepadsCheck && initialGamepadsCheck[0]) {
                console.log("Gamepad già presente all'avvio della pagina. Clicca 'Avvia Audio e Controlli Gamepad'.");
                gamepadStatusSpan.textContent = 'Potenzialmente Connesso';
                gamepadStatusSpan.className = '';
                gamepadNameSpan.textContent = initialGamepadsCheck[0].id;
            }
        }); 
    </script>
</body>

</html>