output to /qad/stock.txt.
for each in_mstr where in_domain = "10usa"  no-lock:
   find first pt_mstr where pt_domain = "10usa" and pt_part = in_part and pt_sfty_stk <> 0  no-lock no-error.
   if available pt_mstr then do:
       if pt_sfty_stk > in_qty_oh then do:
          put unformatted 
          pt_part "," 
          pt_desc1 ","
          pt_um ","
          pt_sfty_stk ","
          in_qty_oh "," "low"
          skip.
       end.      
       else do:       
            put unformatted
            pt_part ","
            pt_desc1 ","
            pt_um ","
            pt_sfty_stk ","
            in_qty_oh "," "high"
            skip.
                                                           
       end.
   end.   
end.
output close. 