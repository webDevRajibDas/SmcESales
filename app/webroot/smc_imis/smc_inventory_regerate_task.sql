-- copy table for current inventories


truncate table current_inventory_history;

ALTER TABLE current_inventories DISABLE TRIGGER current_inventory_history_data_insert_for_update;
update current_inventories set qty=0;
ALTER TABLE current_inventories ENABLE TRIGGER current_inventory_history_data_insert_for_update;

INSERT INTO 
 current_inventory_history
    (
        store_id,
        inventory_status_id,
        product_id,
		batch_number,
		expire_date,
		m_unit,
		qty,
		updated_at,
		balance,
		inventory_entry_type,
		transaction_date,
		transaction_type_id
	 )
	 select store_id,inventory_status_id,product_id,min(batch_number),min(expire_date),min(m_unit),sum(qty),min(updated_at),0,1,min(transaction_date),min(transaction_type_id)
	from current_inventories group by store_id,inventory_status_id,product_id;

	--- Adjustment challan delete for khulna office

	
	--delete from challans where id in( 8107,8108,8109)
	--delete from challan_details where challan_id in( 8107,8108,8109)
	-------------------------- for comilla: Start ----------------------------
	delete from challans where id in(9915,9774);
	delete from challan_details where challan_id in(9915,9774);

	delete from return_challans where id in(1836,1837);

	delete from return_challan_details where challan_id in(1836,1837);
	
	--------------------------- for comilla: end --------------------------------

	-------------------------- for Barisal: Start ----------------------------
	delete from challans where id in(10007,10005,9993,9957,9943,9866,9861,9853);
	delete from challan_details where challan_id in(10007,10005,9993,9957,9943,9866,9861,9853);
	
	--------------------------- for Barisal: end --------------------------------
	


	update sales_people set name ='Md. Milon Biswash' where id =111;
	update sales_people set name ='Md. Jahangir Alam' where id =120;
	update sales_people set name ='Md. Saiful Islam' where id=144;
	update sales_people set name ='Mohoshin-Ul-Islam' where id=141;
	update sales_people set name ='Md.Shahidul Islam' where id=145;
	update sales_people set name ='Mahmud Reza' where id=173;
	update sales_people set name ='Md. Mozaharul Islam' where id=183;
	update sales_people set name ='Md. Rejaul Karim' where id=20223;
	update sales_people set name ='S.M.Sarowar Hossain' where id=117;
	update sales_people set name ='Mohammad Akmol Hossain' where id=118;
	update sales_people set name ='Md. Sahanur Alam' where id=121;
	update sales_people set name ='Provat Chandra Chakravarty' where id=122;
	update sales_people set name ='Sabbir Ahommad Sajal' where id=128;