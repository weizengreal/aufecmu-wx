<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/5/13
 * Time: 下午11:56
 */

namespace app\home\logic;
use app\home\model\Vipuser;

class Card{

    private $vipuser;

    public function __construct()
    {
        $this->vipuser = new Vipuser();
    }

    public function getOne($openid) {
        return $this->vipuser->getOneRank($openid);
    }

    public function getRank($openid) {
        $arr = array(
            'o16hwwfcPRKulZ2YkuMia-PJUT9w',
            'o16hwwWRRGzrw8Z6mve8h-bPxUMY',
            'o16hwwf3Rn0eIYnDwsIpzI-IkZOc',
            'o16hwwcltUVihs1iX97epRtg_Pwg',
            'o16hwwWItO4oz_9hihVfPfKhUzFw',
            'o16hwwRX1OUbDmfy-O5GuoAOvTrc',
            'o16hwwRpKYeaZdm3SnKX7zWvARjA',
            'o16hwwVIL2QXSD8zIy2pSfDuvniM',
            'o16hwwb9HjRJ9uxDHWqd4FoHdeFI'
        );
        if(in_array($openid,$arr)) {
            return "不参与";
        }
        else {
            return $this->vipuser->getRank($openid);
        }
    }

    public function getPageRank($start) {
        $res = $this->vipuser->getPageRank($start);
        for($i=0;$i<count($res);++$i) {
            $res[$i]['rank']=$start+$i+1;
            $res[$i]['headImgUrl'] = empty($res[$i]['headImgUrl']) ?
                "http://wx.aufe.vip/aufecmu_v4/public/images/headimg/".rand(1,20).".jpg"
                : $res[$i]['headImgUrl'];
        }
        return $res;
    }

}

