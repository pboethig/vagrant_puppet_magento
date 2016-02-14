-- add table prefix if you have one
DELETE FROM eav_attribute WHERE attribute_code = 'cmspages' AND entity_type_id IN
                    (SELECT entity_type_id FROM eav_entity_type WHERE entity_type_code = 'catalog_product');
DELETE FROM eav_attribute WHERE attribute_code = 'cmspages' AND entity_type_id IN (SELECT entity_type_id FROM eav_entity_type WHERE entity_type_code = 'catalog_category');
DROP TABLE IF EXISTS ibrams_cmsextended_cmspage_comment_store;
DROP TABLE IF EXISTS ibrams_cmsextended_cmspage_comment;
DROP TABLE IF EXISTS ibrams_cmsextended_cmspage_product;
DROP TABLE IF EXISTS ibrams_cmsextended_cmspage_category;
DROP TABLE IF EXISTS ibrams_cmsextended_cmspage_store;
DROP TABLE IF EXISTS ibrams_cmsextended_cmspage;
DELETE FROM core_resource WHERE code = 'ibrams_cmsextended_setup';
DELETE FROM core_config_data WHERE path like 'ibrams_cmsextended/%';