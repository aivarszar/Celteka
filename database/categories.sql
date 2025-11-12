-- Sākotnējās kategorijas
-- Latvijas tirgum piemērotas kategorijas

INSERT INTO categories (id, parent_id, name, slug, description, icon, is_active, sort_order) VALUES
(1, NULL, 'Pārtika un dzērieni', 'partika-un-dzērieni', 'Vietējie pārtikas produkti un dzērieni', '🥬', 1, 1),
(2, NULL, 'Amatniecība', 'amatnieciba', 'Rokdarbi un amatniecības izstrādājumi', '🎨', 1, 2),
(3, NULL, 'Pakalpojumi', 'pakalpojumi', 'Dažādi pakalpojumi', '🔧', 1, 3),
(4, NULL, 'Mājai un dārzam', 'majai-un-darzam', 'Produkti mājai un dārzam', '🏡', 1, 4),
(5, NULL, 'Transports', 'transports', 'Transporta pakalpojumi', '🚗', 1, 5);

-- Pārtikas apakškategorijas
INSERT INTO categories (parent_id, name, slug, description, icon, is_active, sort_order) VALUES
(1, 'Augļi un dārzeņi', 'augli-un-darzeni', 'Svaigi augļi un dārzeņi', '🍎', 1, 1),
(1, 'Gaļa un zivis', 'gala-un-zivis', 'Vietējā gaļa un zivis', '🥩', 1, 2),
(1, 'Piena produkti', 'piena-produkti', 'Siers, piens, biezpiens', '🧀', 1, 3),
(1, 'Maize un konditorejas izstrādājumi', 'maize-un-konditorejas', 'Mājās cepti produkti', '🍞', 1, 4),
(1, 'Medus un bišu produkti', 'medus-un-bisu-produkti', 'Dabīgs medus un bišu produkti', '🍯', 1, 5);

-- Amatniecības apakškategorijas
INSERT INTO categories (parent_id, name, slug, description, icon, is_active, sort_order) VALUES
(2, 'Keramika', 'keramika', 'Keramikas izstrādājumi', '🏺', 1, 1),
(2, 'Koka izstrādājumi', 'koka-izstradajumi', 'Galdniecība un koka dekori', '🪵', 1, 2),
(2, 'Tekstilizstrādājumi', 'tekstilizstradajumi', 'Šūti un adīti izstrādājumi', '🧵', 1, 3),
(2, 'Rotaslietas', 'rotaslietas', 'Roku darbs rotaslietas', '💍', 1, 4);

-- Pakalpojumu apakškategorijas
INSERT INTO categories (parent_id, name, slug, description, icon, is_active, sort_order) VALUES
(3, 'Remonta pakalpojumi', 'remonta-pakalpojumi', 'Dažādi remonta darbi', '🔨', 1, 1),
(3, 'Kopbraukšana', 'kopbrauksana', 'Koplietošanas braucieni', '🚗', 1, 2),
(3, 'Pasākumu organizēšana', 'pasakumu-organizesana', 'Pasākumi un svinības', '🎉', 1, 3),
(3, 'Mācības un apmācības', 'macibas-un-apmacibas', 'Dažādi kursi un nodarbības', '📚', 1, 4);
