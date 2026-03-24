<?php
require_once 'config.php';

$canzoni = [];

$error_message = '';

$sql = "SELECT id, titolo, artista, link FROM canzoni ORDER BY titolo ASC";

if ($result = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $canzoni[] = $row;
        }
        mysqli_free_result($result);
    } else {
        $error_message = 'Nessuna canzone trovata nel database.';
    }
} else {
    $error_message = 'Errore nell\'esecuzione della query: ' . mysqli_error($conn);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La mia playlist</title>
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
            --list-bg: #282828; 
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
            justify-content: center;
            overflow-x: hidden;
        }

        h1 {
            color: var(--text-color);
            text-align: center;
            margin-top: 50px;
            font-size: 2.5em;
            letter-spacing: -0.05em;
            font-weight: 700;
        }

        p {
            text-align: center;
            margin-bottom: 40px;
            color: var(--light-text-color);
            font-size: 1.1em;
        }

        #canzoni-list {
            list-style: none;
            padding: 0;
            background-color: var(--list-bg);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            max-width: 800px;
            width: 90%;
            margin: 30px auto;
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        #canzoni-list li {
            padding: 20px 30px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
            cursor: pointer; 
        }

        #canzoni-list li:hover {
            background-color: #3a3a3a;
            transform: translateY(-2px);
        }

        #canzoni-list li:last-child {
            border-bottom: none;
        }

        .song-info {
            flex-grow: 1; 
        }

        .song-title {
            text-decoration: none; 
            color: var(--text-color);
            font-weight: 700;
            font-size: 1.3em;
            display: block; 
            word-break: break-word;
            transition: color 0.2s ease-in-out;
        }

        .song-title:hover {
            color: var(--accent-color);
        }

        .artist {
            font-size: 0.9em;
            color: var(--light-text-color);
            margin-top: 5px;
            display: block;
        }

        .message, .no-songs {
            text-align: center;
            font-size: 1.2em;
            color: var(--light-text-color);
            margin-top: 50px;
            padding: 20px;
            background-color: var(--list-bg);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 600px;
        }

        .message {
            color: var(--accent-color);
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2em;
            }
            p {
                font-size: 1em;
            }
            #canzoni-list li {
                padding: 15px 20px;
            }
            .song-title {
                font-size: 1.2em;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.8em;
                margin-top: 30px;
            }
            p {
                font-size: 0.9em;
                margin-bottom: 30px;
            }
            #canzoni-list {
                border-radius: 10px;
            }
            #canzoni-list li {
                padding: 10px 15px;
            }
            .song-title {
                font-size: 1.1em;
            }
            .artist {
                font-size: 0.8em;
            }
        }
    </style>
</head>
<body>
    <h1>Seleziona una traccia per iniziare</h1>
    <p>Clicca su una canzone per accedere agli strumenti di modifica.</p>

    <ul id="canzoni-list">
        <?php if (!empty($error_message)): ?>
            <li class="message"><?php echo $error_message; ?></li>
        <?php elseif (empty($canzoni)): ?>
            <li class="no-songs">Nessuna canzone trovata. Aggiungine qualcuna nel database!</li>
        <?php else: ?>
            <?php foreach ($canzoni as $canzone): ?>
                <li onclick="location.href='modifica.php?id=<?php echo htmlspecialchars($canzone['id']); ?>'">
                    <div class="song-info">
                        <span class="song-title">
                            <?php echo htmlspecialchars($canzone['titolo']); ?>
                        </span>
                        <?php if (!empty($canzone['artista'])): ?>
                            <span class="artist"><?php echo htmlspecialchars($canzone['artista']); ?></span>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</body>
</html>