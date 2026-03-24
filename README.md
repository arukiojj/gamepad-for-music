🎮 Gamepad for Music
Gamepad for Music è un'applicazione web Full-Stack progettata per esplorare nuove modalità di interazione uomo-macchina. Il progetto permette di manipolare e controllare una libreria musicale salvata su database attraverso l'uso di un Joystick/Gamepad.

🚀 Caratteristiche principali
- Utilizzo delle Gamepad API per mappare gli input analogici e digitali del controller.
- Sistema di gestione brani basato su PHP e MySQL.
- Controllo dinamico delle tracce audio (Play/Pause, Volume, Panning) tramite le levette e i tasti del gamepad.
- Sviluppato e testato in ambiente locale tramite XAMPP.

🛠️ Tecnologie Utilizzate
Frontend: HTML5, CSS3, JavaScript (Gamepad & Web Audio API).
Backend: PHP.
Database: MySQL / MariaDB.
Ambiente di sviluppo: XAMPP.

📂 Struttura del Progetto
cc/
├── canzoni/            # Cartella contenente i file audio
├── config.php          # Configurazione parametri di connessione al database
├── database.sql        # Dump SQL per la creazione delle tabelle
├── index.php           # Dashboard principale e gestione input Joystick
├── modifica.php        # Script per la modifica dei metadati delle canzoni
└── imgjoystick.webp    # Asset grafico per la visualizzazione del controller

⚙️ Installazione e Utilizzo
Clonare il repository nella cartella htdocs di XAMPP.
Importare il file database.sql tramite phpMyAdmin.
Configurare le credenziali di accesso al database nel file di connessione PHP.
Avviare il pannello di controllo XAMPP (Apache & MySQL).
Collegare un Gamepad al PC e aprire localhost/gamepad-for-music nel browser.

📌 Note sull'Autore
Progetto realizzato come caso studio personale per approfondire l'integrazione tra periferiche hardware e applicativi web dinamici.
