-- Latvijas ģeogrāfiskās lokācijas
-- Valsts, reģioni un galvenās pilsētas

-- Latvija
INSERT INTO locations (id, parent_id, name, type, code) VALUES (1, NULL, 'Latvija', 'country', 'LV');

-- Reģioni
INSERT INTO locations (parent_id, name, type, code) VALUES
(1, 'Rīgas reģions', 'region', 'LV-RIG'),
(1, 'Vidzemes reģions', 'region', 'LV-VID'),
(1, 'Kurzemes reģions', 'region', 'LV-KUR'),
(1, 'Zemgales reģions', 'region', 'LV-ZEM'),
(1, 'Latgales reģions', 'region', 'LV-LAT');

-- Rīgas reģiona pilsētas
INSERT INTO locations (parent_id, name, type) VALUES
((SELECT id FROM locations WHERE code = 'LV-RIG'), 'Rīga', 'city'),
((SELECT id FROM locations WHERE code = 'LV-RIG'), 'Jūrmala', 'city'),
((SELECT id FROM locations WHERE code = 'LV-RIG'), 'Sigulda', 'city'),
((SELECT id FROM locations WHERE code = 'LV-RIG'), 'Ogre', 'city');

-- Vidzemes reģiona pilsētas
INSERT INTO locations (parent_id, name, type) VALUES
((SELECT id FROM locations WHERE code = 'LV-VID'), 'Valmiera', 'city'),
((SELECT id FROM locations WHERE code = 'LV-VID'), 'Cēsis', 'city'),
((SELECT id FROM locations WHERE code = 'LV-VID'), 'Limbaži', 'city'),
((SELECT id FROM locations WHERE code = 'LV-VID'), 'Madona', 'city');

-- Kurzemes reģiona pilsētas
INSERT INTO locations (parent_id, name, type) VALUES
((SELECT id FROM locations WHERE code = 'LV-KUR'), 'Liepāja', 'city'),
((SELECT id FROM locations WHERE code = 'LV-KUR'), 'Ventspils', 'city'),
((SELECT id FROM locations WHERE code = 'LV-KUR'), 'Kuldīga', 'city'),
((SELECT id FROM locations WHERE code = 'LV-KUR'), 'Talsi', 'city');

-- Zemgales reģiona pilsētas
INSERT INTO locations (parent_id, name, type) VALUES
((SELECT id FROM locations WHERE code = 'LV-ZEM'), 'Jelgava', 'city'),
((SELECT id FROM locations WHERE code = 'LV-ZEM'), 'Bauska', 'city'),
((SELECT id FROM locations WHERE code = 'LV-ZEM'), 'Dobele', 'city'),
((SELECT id FROM locations WHERE code = 'LV-ZEM'), 'Jēkabpils', 'city');

-- Latgales reģiona pilsētas
INSERT INTO locations (parent_id, name, type) VALUES
((SELECT id FROM locations WHERE code = 'LV-LAT'), 'Daugavpils', 'city'),
((SELECT id FROM locations WHERE code = 'LV-LAT'), 'Rēzekne', 'city'),
((SELECT id FROM locations WHERE code = 'LV-LAT'), 'Krāslava', 'city'),
((SELECT id FROM locations WHERE code = 'LV-LAT'), 'Ludza', 'city');
