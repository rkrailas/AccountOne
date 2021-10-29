<?php 

    use Illuminate\Support\Facades\DB;

    function getTaxRate()
    {
        return 7;
    }

    function getGlNunber($bookid)
    {
        $newGlNo = "";
        $data = DB::table('misctable')
                ->select('ram_lastglnumber', 'ram_prefix_glnumber')
                ->where('tabletype', 'JR')
                ->where('code', $bookid)
                ->get();
        $data2 = explode("-" , $data[0]->ram_lastglnumber);        

        if (count($data2)){
            if ($data2[0] == $data[0]->ram_prefix_glnumber . date_format(now(),"ym")){

                //Find max number and plus 1
                $strsql = "select max(a.gltran) as gltran from
                        (select max(gltran) as gltran from gltran where gltran ilike '" . $data2[0] . "%'
                        union
                        select max(gltran) as gltran from glmast where gltran ilike '" . $data2[0] . "%') a";
                $maxExistNumber = DB::select($strsql);
                $data3 = explode("-" , $maxExistNumber[0]->gltran);
                $maxExistNumber = intval($data3[1]) + 1;
                $maxExistNumber = $data3[0] . "-" . sprintf("%06d", $maxExistNumber);

                //Create new number
                $newGlNo = intval($data2[1]) + 1;
                $newGlNo = $data2[0] . "-" . sprintf("%06d", $newGlNo);

                //compare $maxExistNumber vs $newDocNo
                if ($maxExistNumber < $newGlNo){
                    $newGlNo = $maxExistNumber;
                }

                DB::statement("UPDATE misctable SET ram_lastglnumber=? where tabletype=? and code=?"
                , [$newGlNo,"JR",$bookid]);
            }else{
                $newGlNo = $data[0]->ram_prefix_glnumber . date_format(now(),"ym") . "-000001";

                DB::statement("UPDATE misctable SET ram_lastglnumber=? where tabletype=? and code=?"
                , [$newGlNo,"JR",$bookid]);
            }
        }
        return $newGlNo;
    }

    function getDocNunber($bookid)
    {
        $newDocNo = "";
        $data = DB::table('misctable')
                ->select('ram_lastdocnumber', 'ram_prefix_docnumber')
                ->where('tabletype', 'JR')
                ->where('code', $bookid)
                ->get();
        $data2 = explode("-" , $data[0]->ram_lastdocnumber);

        if (count($data2)){
            if ($data2[0] == $data[0]->ram_prefix_docnumber . date_format(now(),"ym")){

                //Find max number and plus 1
                $maxExistNumber = DB::table('sales')
                ->where('snumber', 'ilike', $data2[0].'%')
                ->max('snumber');
                $data3 = explode("-" , $maxExistNumber);
                $maxExistNumber = intval($data3[1]) + 1;
                $maxExistNumber = $data3[0] . "-" . sprintf("%06d", $maxExistNumber);

                //Create new number
                $newDocNo = intval($data2[1]) + 1;
                $newDocNo = $data2[0] . "-" . sprintf("%06d", $newDocNo);

                //compare $maxExistNumber vs $newDocNo
                if ($maxExistNumber < $newDocNo){
                    $newDocNo = $maxExistNumber;
                }

                DB::statement("UPDATE misctable SET ram_lastdocnumber=? where tabletype=? and code=?"
                , [$newDocNo,"JR",$bookid]);
            }else{
                $newDocNo = $data[0]->ram_prefix_docnumber . date_format(now(),"ym") . "-000001";

                DB::statement("UPDATE misctable SET ram_lastdocnumber=? where tabletype=? and code=?"
                , [$newDocNo,"JR",$bookid]);
            }
        }
        return $newDocNo;
    }

    function getTaxNunber($bookid) //Support SO, SC
    {
        $newDocNo = "";
        $data = DB::table('misctable')
                ->select('ram_lasttaxnumber', 'ram_prefix_taxnumber')
                ->where('tabletype', 'JR')
                ->where('code', $bookid)
                ->get();
        $data2 = explode("-" , $data[0]->ram_lasttaxnumber);        

        if (count($data2)){
            if ($data2[0] == $data[0]->ram_prefix_taxnumber . date_format(now(),"ym")){
                
                //Find max number and plus 1
                $maxExistNumber = DB::table('taxdata')
                ->where('taxnumber', 'ilike', $data2[0].'%')
                ->max('taxnumber');
                $data3 = explode("-" , $maxExistNumber);
                $maxExistNumber = intval($data3[1]) + 1;
                $maxExistNumber = $data3[0] . "-" . sprintf("%06d", $maxExistNumber);

                //Create new number
                $newDocNo = intval($data2[1]) + 1;
                $newDocNo = $data2[0] . "-" . sprintf("%06d", $newDocNo);

                //compare $maxExistNumber vs $newDocNo
                if ($maxExistNumber < $newDocNo){
                    $newDocNo = $maxExistNumber;
                }

                DB::statement("UPDATE misctable SET ram_lasttaxnumber=? where tabletype=? and code=?"
                , [$newDocNo,"JR",$bookid]);
            }else{
                $newDocNo = $data[0]->ram_prefix_taxnumber . date_format(now(),"ym") . "-000001";

                DB::statement("UPDATE misctable SET ram_lasttaxnumber=? where tabletype=? and code=?"
                , [$newDocNo,"JR",$bookid]);
            }
        }
        return $newDocNo;
    }
