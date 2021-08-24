<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReverseController
 * @Route("reverse/")
 */
class ReverseController extends BaseController
{


    public function isStrUnique(String $str)
    {
        for ($i = 0; $i < strlen($str); $i++) {
            for ($j = $i + 1; $j < strlen($str); $j++) {
                if ($str[$i] == $str[$j]) {
                    return false;
                }
            }
        }
        return true;
    }

    public function isEqual(String $str1, String $str2)
    {
        if (preg_match('~^\p{Lu}~u', $str1) && preg_match('~^\p{Lu}~u', $str2)) {
            return $str1 == $str2;
        } else if (preg_match('~^\p{Lu}~u', $str1) && !preg_match('~^\p{Lu}~u', $str2)) {
            return $str1 == strtoupper($str2);
        } else if (!preg_match('~^\p{Lu}~u', $str1) && preg_match('~^\p{Lu}~u', $str2)) {
            return strtoupper($str1) == $str2;
        } else {
            return $str1 == $str2;
        }
    }

    public function getRepeated(String $str, array $res)
    {
        $repeatedStr = '';
        $count = 0;
        $currRev = array();
        for ($i = 0; $i < strlen($str); $i++) {
            $count = 1;
            for ($j = $i + 1; $j < strlen($str); $j++) {
                $currentTip = $str[$i];
                $nextTip = $str[$j];
                if ($this->isEqual($currentTip, $nextTip) && $str[$i] != '') {
                    if (!in_array($str[$i], $currRev) && !in_array(strtolower($str[$i]), $currRev)) {
                        $count++;
                        array_push($currRev, $str[$j]);
                        $str[$j] = '*';
                    }
                }
            }
            if ($count > 1 && $str[$i] != '*') {
                $repeatedStr .= $str[$i];
            }
        }
        array_push($res, strrev($repeatedStr));
        $endFilter = str_replace("*", "", $str);
        if ($this->isStrUnique($endFilter)) {
            return [strrev($endFilter), ...$res];
        } else {
            return $this->getRepeated($endFilter, $res);
        }
    }


    /**
     * 
     * @Route("", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Reverse string",
     *   
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description="Request body"
     * )
     * @SWG\Tag(name="Reverse")
     * @param Request $request
     * @return Response
     */
    public function addContact(Request $request)
    {
        $body = $request->get('body', null);
        $resArr = array();
        $result = $this->getRepeated($body, $resArr);
        return $this->success($result, ["Default"]);
    }
}
