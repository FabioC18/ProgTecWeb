CREATE TABLE IF NOT EXISTS public.contenuti
(
    id SERIAL PRIMARY KEY,
    titolo text NOT NULL,
    descrizione text NOT NULL,
    immagine text NOT NULL
);

ALTER TABLE contenuti OWNER TO www;

INSERT INTO contenuti (titolo, descrizione, immagine) VALUES 
('Suite', '4 persone, vasca idromassaggio, sauna', 'suite1.jpeg'),
('Deluxe', '2 persone, vasca', 'deluxe.jpeg'),
('Pacchetti', 'Pacchetti per aperitivi romantici', 'ape3.jpeg');