INSERT INTO "counterparty" ("code", "description", "is_folder", "is_deleted", "parent_id", "version", "author_id") VALUES ('00000001', 'Юр.лица', TRUE, FALSE, NULL, NOW(), 1);
INSERT INTO "counterparty" ("code", "description", "is_folder", "is_deleted", "parent_id", "version", "author_id") VALUES ('00000002', 'ООО Одуванчик', FALSE, FALSE, 1, NOW(), 1);
INSERT INTO "counterparty" ("code", "description", "is_folder", "is_deleted", "parent_id", "version", "author_id") VALUES ('00000003', 'ЗАО Фабула', FALSE, FALSE, 1, NOW(), 1);

INSERT INTO "product" ("code", "description", "is_folder", "is_deleted", "parent_id", "version", "author_id") VALUES ('00000001', 'Ноутбуки', TRUE, FALSE, NULL, NOW(), 1);
INSERT INTO "product" ("code", "description", "is_folder", "is_deleted", "parent_id", "version", "author_id") VALUES ('00000002', 'Apple MacBook', FALSE, FALSE, 1, NOW(), 1);
INSERT INTO "product" ("code", "description", "is_folder", "is_deleted", "parent_id", "version", "author_id") VALUES ('00000003', 'Acer', FALSE, FALSE, 1, NOW(), 1);

INSERT INTO "warehouse" ("code", "description", "is_deleted", "version", "author_id") VALUES ('00000002', 'Склад #2', FALSE, NOW(), 1);
INSERT INTO "warehouse" ("code", "description", "is_deleted", "version", "author_id") VALUES ('00000003', 'Склад #2', FALSE, NOW(), 1);

INSERT INTO "document_coming_of_goods" 
("number", "date_time", "is_deleted", "is_posted", "counterparty_id", "warehouse_id", "version", "author_id") 
VALUES ('00000001', NOW(), FALSE, TRUE, 2, 1, NOW(), 1); 
INSERT INTO "document_coming_of_goods_product" ("document_id", "line_number", "product_id", "quantity", "price") VALUES (1, 1, 2, '1', '50000');
INSERT INTO "document_coming_of_goods_product" ("document_id", "line_number", "product_id", "quantity", "price") VALUES (1, 2, 3, '1', '45000');
