CREATE TABLE IF NOT EXISTS public.pacchetti
(
    id SERIAL PRIMARY KEY,
    nome text NOT NULL,
    descrizione text NOT NULL,
    prezzo numeric(6,2),
    immagine text   
);

ALTER TABLE pacchetti OWNER TO www;


INSERT INTO pacchetti (nome, descrizione, prezzo, immagine) VALUES 
('Pacchetto Base', 'Allestimento love standard per una serata romantica.', 19, 'packbase.jpeg'),
('Love Base', 'Allestimento + aperitivo con stuzzichini locali.', 29, 'love-base.jpeg'),
('Love Plus', 'Allestimento + aperitivo large con bottiglia di spumante.', 39, 'ape3.jpeg'),
('Love Sushi', 'Allestimento + box sushi speciale per due persone.', 49, 'imgsushi.jpeg');