-- migration_add_category_to_gallery.sql
-- Script de migration pour ajouter la colonne category_id à la table gallery
-- À exécuter si la table existe déjà

ALTER TABLE gallery 
ADD COLUMN category_id INT NULL AFTER image_path,
ADD INDEX idx_category_id (category_id),
ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

