/* Delivery Note Print + Email xxdorp_mail.p                                   */
/* Copyright 1986 QAD Inc., Carpinteria, CA, USA.                              */
/* All rights reserved worldwide. This is an unpublished work.                 */
/* REVISION: 1.0           LAST MODIFIED: 08/22/19           BY: fga           */
/*-----------------------------------------------------------------------------*/

         /* DISPLAY TITLE */
/*K0M9*/ {mfdtitle.i "A "} /*G510*/

define NEW SHARED VARIABLE sonbr        like dss_nbr.
define NEW SHARED variable sonbr1       like dss_nbr.
DEFINE NEW SHARED VARIABLE shipdate     like so_ship_date initial TODAY.
define NEW SHARED variable tran         as character format "x(15)".
define NEW SHARED variable vehicle      as character format "x(15)".
define NEW SHARED variable Prepare      as character format "x(12)".
define NEW SHARED variable Appr         as character format "x(12)".
define NEW SHARED variable secure       as character format "x(12)".
define NEW SHARED variable drvr         as character format "x(12)".
define NEW SHARED variable rcpt         as character format "x(12)".
define new shared variable surat_jalan  like ts_sp_nbr.
define new shared variable surat_jalan1 like ts_sp_nbr.

define variable lvl_sendmail            as logical                      no-undo.
define variable lvc_email               as character                    no-undo.
define variable lvc_mailuser            as character                    no-undo.
define variable lvc_headmail            as character                    no-undo.
define variable lvi_count               as integer                      no-undo.

define variable lvc_rcpmail             as character                    no-undo.
define variable lvc_attachfile          as character                    no-undo.
define variable lvc_submail             as character                    no-undo.
define variable lvc_outputtxt           as character                    no-undo.

define new shared variable addr         as character format "x(38)" extent 6.
         
         /*FORM SKIP
           sonbr    colon 25 sonbr1          label "To"
           shipdate        COLON 25 SKIP
        with frame a side-labels width 80 THREE-D.
        */

form skip
    surat_jalan    colon 25 
    surat_jalan1   label "To" Skip(1)
    lvl_sendmail   colon 25 label "Sent Email?"  SKIP
with frame a side-labels width 80 THREE-D.


surat_jalan   = "19082768".
surat_jalan1  = "19082769".

repeat:

  if surat_jalan1 = hi_char then surat_jalan1 = "".

  update
    surat_jalan
    surat_jalan1 
    lvl_sendmail 
  with frame a.

  if surat_jalan1 = "" then surat_jalan1 = hi_char.

  /* SELECT PRINTER */
    {mfselprt.i "printer" 132}
    /* {mfphead.i} */

    /*AP  {gprun.i ""xxdorp01_ap.p""} */
    /*sg*/ /*{gprun.i ""xxdorp01_sg.p""}    */
    /*a3i*/ {gprun.i ""xxdn_print_sub.p""}  

  for each tssp_mstr where (ts_sp_nbr >= surat_jalan
                       and  ts_sp_nbr <= surat_jalan1) no-lock: 
            
    for each so_mstr where so_domain = global_domain
                       and so_nbr = ts_sp_so
                        NO-LOCK:

      for each sp_mstr where sp_addr = so_slspsn[1] no-lock: /* key nya belum tau, SO dengan Salespersson */
 
        if available sp_mstr then do:
          assign
          lvc_headmail = lvc_headmail + sp__chr01 + " "
          lvc_headmail = lvc_headmail + sp__chr02 + " "
          lvc_headmail = lvc_headmail + sp__chr03 + " "
          lvc_headmail = lvc_headmail + sp__chr04 + " ".
        end.

      end.

      find first cd_det where cd_domain = global_domain
                          and cd_ref = so_cust no-lock no-error.
    
        if available cd_det then do:

          do lvi_count = 1 to 15:

            if cd_cmmt[lvi_count] <> "" then
               lvc_mailuser = lvc_mailuser + cd_cmmt[lvi_count] + " ".

          end.

        end.

      assign
        surat_jalan  = ts_sp_nbr.
        surat_jalan1 = ts_sp_nbr.

      if lvl_sendmail = yes then do:
       
        output to value("/usr/qad/spemail/" + ts_sp_nbr + ".txt").
          {gprun.i ""xxsub_email.p""}
        output close.


      end.


        lvc_rcpmail    = lvc_headmail.
        lvc_rcpmail    = lvc_rcpmail + " " + lvc_mailuser.


        lvc_attachfile = "/usr/qad/spemail/" + ts_sp_nbr + ".txt" .
        lvc_submail    = "Notifikasi Pengiriman" + " " + ts_sp_po + " / " + ts_sp_so.
        lvc_outputtxt  = "/usr/qad/admin/email" + " -t " 
                       + lvc_rcpmail + " -u " + '"' 
                       + lvc_submail + '"' + " -a " 
                       + lvc_attachfile + " -o message-file=/usr/qad/spemail/ebody.txt" .

        UNIX SILENT VALUE(lvc_outputtxt).


        lvc_rcpmail  = "".
        lvc_headmail = "".
        lvc_mailuser = "".



    end. /*end of so_mstr*/
  end. /*end of tssp_mstr*/

  {mfreset.i}
        
end. /* end of repeat */





