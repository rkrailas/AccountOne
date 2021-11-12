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

                //Find max number and plus 1 ยังมี Error กรณีเลขที่ misctable กับ gltran + glmast ติดกัน
                // $strsql = "select max(a.gltran) as gltran from
                //         (select max(gltran) as gltran from gltran where gltran ilike '" . $data2[0] . "%'
                //         union
                //         select max(gltran) as gltran from glmast where gltran ilike '" . $data2[0] . "%') a";
                // $maxExistNumber = DB::select($strsql);

                // if ($maxExistNumber && $maxExistNumber[0]->gltran != null) {
                //     $data3 = explode("-" , $maxExistNumber[0]->gltran);
                //     $maxExistNumber = intval($data3[1]) + 1;
                //     $maxExistNumber = $data3[0] . "-" . sprintf("%06d", $maxExistNumber);
    
                //     //Create new number
                //     $newGlNo = intval($data2[1]) + 1;
                //     $newGlNo = $data2[0] . "-" . sprintf("%06d", $newGlNo);
    
                //     //compare $maxExistNumber vs $newDocNo
                //     if ($maxExistNumber < $newGlNo){
                //         $newGlNo = $maxExistNumber;
                //     }
                // }else{
                //     //Create new number
                //     $newGlNo = intval($data2[1]) + 1;
                //     $newGlNo = $data2[0] . "-" . sprintf("%06d", $newGlNo);
                // }

                //Create new number
                $newGlNo = intval($data2[1]) + 1;
                $newGlNo = $data2[0] . "-" . sprintf("%06d", $newGlNo);

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

                //Create new number
                $newDocNo = intval($data2[1]) + 1;
                $newDocNo = $data2[0] . "-" . sprintf("%06d", $newDocNo);

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

                //Create new number
                $newDocNo = intval($data2[1]) + 1;
                $newDocNo = $data2[0] . "-" . sprintf("%06d", $newDocNo);

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

    function getCnDocNunber($bookid)
    {
        $newDocNo = "";
        $data = DB::table('misctable')
                ->select('ram_lastcndocnumber', 'ram_prefix_cndocnumber')
                ->where('tabletype', 'JR')
                ->where('code', $bookid)
                ->get();
        $data2 = explode("-" , $data[0]->ram_lastcndocnumber);

        if (count($data2)){
            if ($data2[0] == $data[0]->ram_prefix_cndocnumber . date_format(now(),"ym")){

                //Create new number
                $newDocNo = intval($data2[1]) + 1;
                $newDocNo = $data2[0] . "-" . sprintf("%06d", $newDocNo);

                DB::statement("UPDATE misctable SET ram_lastcndocnumber=? where tabletype=? and code=?"
                , [$newDocNo,"JR",$bookid]);
            }else{
                $newDocNo = $data[0]->ram_prefix_cndocnumber . date_format(now(),"ym") . "-000001";

                DB::statement("UPDATE misctable SET ram_lastcndocnumber=? where tabletype=? and code=?"
                , [$newDocNo,"JR",$bookid]);
            }
        }
        return $newDocNo;
    }

    function getCnTaxNunber($bookid)
    {
        $newDocNo = "";
        $data = DB::table('misctable')
                ->select('ram_lastcntaxnumber', 'ram_prefix_cntaxnumber')
                ->where('tabletype', 'JR')
                ->where('code', $bookid)
                ->get();
        $data2 = explode("-" , $data[0]->ram_lastcntaxnumber);        

        if (count($data2)){
            if ($data2[0] == $data[0]->ram_prefix_cntaxnumber . date_format(now(),"ym")){

                //Create new number
                $newDocNo = intval($data2[1]) + 1;
                $newDocNo = $data2[0] . "-" . sprintf("%06d", $newDocNo);

                DB::statement("UPDATE misctable SET ram_lastcntaxnumber=? where tabletype=? and code=?"
                , [$newDocNo,"JR",$bookid]);
            }else{
                $newDocNo = $data[0]->ram_prefix_cntaxnumber . date_format(now(),"ym") . "-000001";

                DB::statement("UPDATE misctable SET ram_lastcntaxnumber=? where tabletype=? and code=?"
                , [$newDocNo,"JR",$bookid]);
            }
        }
        return $newDocNo;
    }