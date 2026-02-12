CREATE TABLE IF NOT EXISTS public.utenti (
    id SERIAL PRIMARY KEY,
    username text NOT NULL,
    email text NOT NULL,
    password text NOT NULL
);

ALTER TABLE utenti OWNER TO www;