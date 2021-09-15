<?php 

    function getGlNunber($bookid)
    {
        $newGlNo = "";
        $data = DB::table('misctable')
                ->select('lastglnumber', 'prefix_lastglnumber')
                ->where('tabletype', 'JR')
                ->where('code', $bookid)
                ->get();
        $data2 = explode("-" , $data[0]->lastglnumber);        

        if (count($data2)){
            if ($data2[0] == $data[0]->prefix_lastglnumber . date_format(now(),"ym")){
                $newGlNo = intval($data2[1]) + 1;
                $newGlNo = $data2[0] . "-" . sprintf("%06d", $newGlNo);

                DB::statement("UPDATE misctable SET lastglnumber=? where tabletype=? and code=?"
                , [$newGlNo,"JR",$bookid]);
            }else{
                $newGlNo = $data[0]->prefix_lastglnumber . date_format(now(),"ym") . "-000001";

                DB::statement("UPDATE misctable SET lastglnumber=? where tabletype=? and code=?"
                , [$newGlNo,"JR",$bookid]);
            }
        }
        return $newGlNo;
    }

    function getDocNunber($bookid)
    {
        $newDocNo = "";
        $data = DB::table('misctable')
                ->select('lastdocnumber', 'prefix_lastdocnumber')
                ->where('tabletype', 'JR')
                ->where('code', $bookid)
                ->get();
        $data2 = explode("-" , $data[0]->lastdocnumber);        

        if (count($data2)){
            if ($data2[0] == $data[0]->prefix_lastdocnumber . date_format(now(),"ym")){
                $newDocNo = intval($data2[1]) + 1;
                $newDocNo = $data2[0] . "-" . sprintf("%06d", $newDocNo);

                DB::statement("UPDATE misctable SET lastdocnumber=? where tabletype=? and code=?"
                , [$newDocNo,"JR",$bookid]);
            }else{
                $newDocNo = $data[0]->prefix_lastdocnumber . date_format(now(),"ym") . "-000001";

                DB::statement("UPDATE misctable SET lastdocnumber=? where tabletype=? and code=?"
                , [$newDocNo,"JR",$bookid]);
            }
        }
        return $newDocNo;
    }
