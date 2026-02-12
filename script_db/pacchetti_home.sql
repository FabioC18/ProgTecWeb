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
('Pacchetto Base', 'Allestimento love standard per una serata romantica.', 49.99, 'packbase.jpeg'),
('Love Base', 'Allestimento + aperitivo con stuzzichini locali.', 79.99, 'love-base.jpeg'),
('Love Plus', 'Allestimento + aperitivo large con bottiglia di spumante.', 119.99, 'ape3.jpeg'),
('Love Sushi', 'Allestimento + box sushi speciale per due persone.', 149.99, 'imgsushi.jpeg');