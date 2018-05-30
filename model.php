<?php

/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/5/29
 * Time: 下午4:11
 */
class model
{
    protected $db;
    protected $res;

    /*构造函数*/
    function __construct(){
        $this->db = new pdo_mysql(array(
            "dsn"=>DSN,
            "name"=>NAME,
            "password"=>PASS
        ));
    }

    public function  read_by_keyword($keyword){
        return $this->db->select(0,1,"main","url","and keyword='".$keyword."'","tid desc");
    }

    public function  read_by_url($url){
        return $this->db->select(0,1,"main","keyword","and url='".$url."'","tid desc");
    }

    public function  write_for_url($url){

        $_keyword = $this->read_by_url($url);

        if(!$_keyword) {
            try {
                $_res = $this->db->select(0, 1, "main", "max(tid)", "and 1=1", "tid desc");
                $_res = json_decode(json_encode($_res), TRUE);
                $_tid = $_res["max(tid)"];
                $_tid = empty($_tid) || !isset($_tid) ? 1000000 : $_tid;
                $_tid = intval($_tid)<1000000?1000000:$_tid;
                $_keyword = $this->from10_to62($_tid . "");
                $res = $this->db->insert(0, 1, "main", array(
                    'url = "' . $url . '"', //源链接
                    'keyword = "' . $_keyword . '"',//短链接码
                    'type = "system"', //系统: “system” 自定义: “custom”
                    'insert_at =' . time(), //插入时间
                    'updated_at =' . time() //更新时间
                ));

                if ($res == 1) {
                    return $_keyword;
                } else {
                    return "undefined";
                }
            }catch(Exception $err){
                return $err;
            }
        }else{
            $_keyword = json_decode(json_encode($_keyword),TRUE);
            return $_keyword["keyword"];
        }
    }

    /**
     * 十进制数转换成62进制
     *
     * @param integer $num
     * @return string
     */
    public function from10_to62($num) {
            $to = 62;
            $dict = '0345aXb67LMNOPQR89cdeVWfghijkHIJlmnUopqrstuAwYxByCzD2EFGK1STvZ';
            $ret = '';
            do {
                $ret = $dict[bcmod($num, $to)] . $ret;
                $num = bcdiv($num, $to);
            } while ($num > 0);
            return $ret;
    }

    /**
     * 62进制数转换成十进制数
     *
     * @param string $num
     * @return string
     */
    public function from62_to10($num) {
        $from = 62;
        $num = strval($num);
        $dict = '0345aXb67LMNOPQR89cdeVWfghijkHIJlmnUopqrstuAwYxByCzD2EFGK1STvZ';
        $len = strlen($num);
        $dec = 0;
        for($i = 0; $i < $len; $i++) {
            $pos = strpos($dict, $num[$i]);
            $dec = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $dec);
        }
        return $dec;
    }

    public function b64dec($b64) { //64进制转换成10进制
        $map = array(
            '0'=>0,'1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5,'6'=>6,'7'=>7,'8'=>8,'9'=>9,
            'A'=>10,'B'=>11,'C'=>12,'D'=>13,'E'=>14,'F'=>15,'G'=>16,'H'=>17,'I'=>18,'J'=>19,
            'K'=>20,'L'=>21,'M'=>22,'N'=>23,'O'=>24,'P'=>25,'Q'=>26,'R'=>27,'S'=>28,'T'=>29,
            'U'=>30,'V'=>31,'W'=>32,'X'=>33,'Y'=>34,'Z'=>35,'a'=>36,'b'=>37,'c'=>38,'d'=>39,
            'e'=>40,'f'=>41,'g'=>42,'h'=>43,'i'=>44,'j'=>45,'k'=>46,'l'=>47,'m'=>48,'n'=>49,
            'o'=>50,'p'=>51,'q'=>52,'r'=>53,'s'=>54,'t'=>55,'u'=>56,'v'=>57,'w'=>58,'x'=>59,
            'y'=>60,'z'=>61,'_'=>62,'='=>63
        );
        $dec = 0;
        $len = strlen($b64);
        for ($i = 0; $i < $len; $i++) {
            $b = $map[$b64{$i}];
            if ($b === NULL) {
                return FALSE;
            }
            $j = $len - $i - 1;
            $dec += ($j == 0 ? $b : (2 << (6 * $j - 1)) * $b);
        }
        return $dec;
    }

    public function decb64($dec) { //10进制转换成64进制
        if ($dec < 0) {
            return FALSE;
        }
        $map = array(
            0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',
            10=>'A',11=>'B',12=>'C',13=>'D',14=>'E',15=>'F',16=>'G',17=>'H',18=>'I',19=>'J',
            20=>'K',21=>'L',22=>'M',23=>'N',24=>'O',25=>'P',26=>'Q',27=>'R',28=>'S',29=>'T',
            30=>'U',31=>'V',32=>'W',33=>'X',34=>'Y',35=>'Z',36=>'a',37=>'b',38=>'c',39=>'d',
            40=>'e',41=>'f',42=>'g',43=>'h',44=>'i',45=>'j',46=>'k',47=>'l',48=>'m',49=>'n',
            50=>'o',51=>'p',52=>'q',53=>'r',54=>'s',55=>'t',56=>'u',57=>'v',58=>'w',59=>'x',
            60=>'y',61=>'z',62=>'_',63=>'=',
        );
        $b64 = '';
        do {
            $b64 = $map[($dec % 64)] . $b64;
            $dec /= 64;
        } while ($dec >= 1);
        return $b64;
    }


}