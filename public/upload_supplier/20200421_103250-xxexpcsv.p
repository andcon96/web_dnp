define variable lvc_desc1 like pt_mstr.pt_desc1.
define variable lvd_amt   as   decimal format "->>>>>>>>9.99".
define variable lvt_exp   as   character format "x(10)".
output to /qad/expitem_refresh.csv.
   for each ld_det 
      where ld_domain = "10usa" 
        and ld_expire > today 
        and ld_expire <=  add-interval (today ,3, 'month') 
   no-lock:
      lvt_exp = string(year(ld_expire)) + "-" + string(month(ld_expire),"99") + "-" + string(day(ld_expire)).
      find first pt_mstr 
          where pt_domain = "10USA" 
            and pt_part   = ld_part
      no-lock no-error.
      if available pt_mstr then 
         assign
            lvc_desc1 = pt_desc1
            lvd_amt = ld_qty_oh * pt_price. 
      
      put unformatted ld_part "," 
                      lvc_desc1 ","                       
                      lvt_exp ","
                      ld_loc "," 
                      ld_lot "," 
                      lvd_amt 
                      skip.
   end.
output close.