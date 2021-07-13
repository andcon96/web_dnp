
define input parameter inpdomain as character no-undo.
define input parameter inpart as character no-undo.
define input parameter insite as character no-undo.
define input parameter inloc as character no-undo.

define output parameter outOK as logical no-undo initial false.
define output parameter outMsg as character no-undo initial "".

define variable inpart1 as char.
define variable insite1 as char.
define variable inloc1 as char.

define temp-table temp
field t_part 	as char format "x(100)"
field t_desc    as char format "x(100)"
field t_site 	as char
field t_loc 	as char
field t_lot 	as char
field t_um 		as char
field t_qty 	as decimal
field t_date 	as date.

define output parameter table for temp.

define variable v_um as char.
define variable v_desc as char.

if inloc = "" then inloc1 = "zzz".
else inloc1 = inloc.

if inpart = "" then inpart1 = "zzz".
else inpart1 = inpart.

if insite = "" then insite1 = "zzz".
else insite1 = insite.

for each ld_det where ld_domain = inpdomain 
and ld_site >= insite and ld_site <= insite1
and ld_part >= inpart and ld_part <= inpart1 
and ld_loc >= inloc and ld_loc <= inloc1 no-lock:
	
	find first pt_mstr where pt_domain = inpdomain and pt_part = ld_part
		no-lock no-error.
	if available pt_mstr then do :
		v_um = pt_um.
		v_desc = pt_desc1 + " " + pt_desc2.
	end.
	else do :
		v_um = "".
		v_desc = "".
	end.

	outOK = yes.
	create temp.
	assign
	t_part 	= ld_part
        t_desc  = v_desc
	t_site 	= ld_site
	t_loc 	= ld_loc
	t_lot 	= ld_lot
	t_um 	= v_um
	t_qty 	= ld_qty_oh
	t_date 	= ld_date.
end.

catch eSysError as Progress.Lang.SysError:
    outMsg = eSysError:GetMessage(1).
    delete object eSysError.
end catch.


