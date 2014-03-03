delimiter //

create function getProductPriceOfOrder(import_id int, order_id int) returns decimal(10,4) deterministic
begin
 declare total decimal(10,4);
 set total = 0;
 select sum(iop.price) into total from imported_order_to_products iop where iop.imported_order_id=order_id and iop.import_id=import_id;
 if total is null then
  set total = 0;
 end if;
 return total;
end//

create function getCountOrderOfProduct(import_id int, product_id int) returns int deterministic
begin
 declare total int;
 select count(iop.id) into total from imported_order_to_products iop where iop.imported_product_id=product_id and iop.import_id=import_id;
 if total is null then
  set total = 0;
 end if;
 return total;
end//

create function getCountVisitsOfVisitor(visitor_id int) returns int deterministic
begin
 declare total int;
 select count(iv.id) into total from imported_visits iv where iv.imported_visitor_id=visitor_id;
 if total is null then
  set total = 0;
 end if;
 return total;
end//
