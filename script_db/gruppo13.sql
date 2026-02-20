DO
$$
BEGIN
   IF NOT EXISTS (
      SELECT FROM pg_catalog.pg_roles WHERE rolname = 'www'
   ) THEN
      CREATE ROLE www LOGIN PASSWORD 'www';
   END IF;
END
$$;

CREATE DATABASE gruppo13 WITH
  OWNER = www
  ENCODING = 'UTF8'

\c gruppo13



-- DROP
DROP TABLE IF EXISTS camere CASCADE;
DROP TABLE IF EXISTS utenti CASCADE;
DROP TABLE IF EXISTS pacchetti CASCADE;
DROP TABLE IF EXISTS prenotazioni CASCADE;
DROP TABLE IF EXISTS contenuti CASCADE;


-- UTENTI
CREATE TABLE IF NOT EXISTS public.utenti (
    id SERIAL PRIMARY KEY,
    username text NOT NULL,
    email text NOT NULL,
    password text NOT NULL
);


-- CONTENUTI
CREATE TABLE IF NOT EXISTS public.contenuti
(
    id SERIAL PRIMARY KEY,
    titolo text NOT NULL,
    descrizione text NOT NULL,
    immagine text NOT NULL
);


-- PACCHETTI
CREATE TABLE IF NOT EXISTS public.pacchetti
(
    id SERIAL PRIMARY KEY,
    nome text NOT NULL,
    descrizione text NOT NULL,
    prezzo numeric(6,2),
    immagine text   
);



-- PRENOTAZIONI
CREATE TABLE IF NOT EXISTS public.prenotazioni (
    id SERIAL PRIMARY KEY,
    id_utente INTEGER REFERENCES utenti(id) ON DELETE CASCADE,
    nome_pacchetto text NOT NULL,
    data_prenotazione DATE,
    prezzo NUMERIC(10, 2) NOT NULL
);


-- CAMERE
CREATE TABLE IF NOT EXISTS public.camere (
    id SERIAL PRIMARY KEY,
    titolo VARCHAR(100) NOT NULL,
    descrizione TEXT,
    prezzo NUMERIC(10, 2),
    galleria TEXT NOT NULL 
);

-- PERMESSI
ALTER DEFAULT PRIVILEGES IN SCHEMA public
GRANT ALL ON TABLES TO www;

ALTER DEFAULT PRIVILEGES IN SCHEMA public
GRANT ALL ON SEQUENCES TO www;



ALTER TABLE camere OWNER TO www;
ALTER TABLE utenti OWNER TO www;
ALTER TABLE pacchetti OWNER TO www;
ALTER TABLE prenotazioni OWNER TO www;
ALTER TABLE contenuti OWNER TO www;



INSERT INTO camere (titolo, descrizione, prezzo, galleria) VALUES 
(
    'AREA SUITE', 
    'Scopri la nostra Suite esclusiva con vasca idromassaggio, sauna e cucina moderna. Il massimo del relax', 
    150.00, 
    's1.jpeg,s2.jpeg,s3.jpeg,s4.jpeg'
),
(
    'AREA DELUXE', 
    'Vivi la casa vacanza Deluxe con vasca privata in camera, zona living per cene romantiche e cromoterapia.', 
    100.00, 
    's5.jpeg,s6.jpeg,s7.jpeg,s8.jpeg'
);


INSERT INTO contenuti (titolo, descrizione, immagine) VALUES 
('Suite', '4 persone, vasca idromassaggio, sauna', 'suite1.jpeg'),
('Deluxe', '2 persone, vasca', 'deluxe.jpeg'),
('Pacchetti', 'Pacchetti per aperitivi romantici', 'ape3.jpeg');

INSERT INTO pacchetti (nome, descrizione, prezzo, immagine) VALUES 
('Pacchetto Base', 'Allestimento love standard per una serata romantica.', 49.99, 'packbase.jpeg'),
('Love Base', 'Allestimento + aperitivo con stuzzichini locali.', 79.99, 'love-base.jpeg'),
('Love Plus', 'Allestimento + aperitivo large con bottiglia di spumante.', 119.99, 'ape3.jpeg'),
('Love Sushi', 'Allestimento + box sushi speciale per due persone.', 149.99, 'imgsushi.jpeg');