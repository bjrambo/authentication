/* vi:set ts=4 sw=4 expandtab enc=utf8: */
global_flag = true;

var country_codes = [
"233" // 가나
, "241" // 가봉
, "220" // 감비아
, "264" // 나미비아

, "234" // 나이지리아
, "27" // 남아공화국
, "227" // 니제르
, "231" // 라이베리아
, "266" // 레소토
, "250" // 르완다

, "261" // 마다가스카르
, "265" // 말라위
, "223" // 말리
, "212" // 모로코
, "230" // 모리셔스
, "222" // 모리타니

, "258" // 모잠비크
, "267" // 보츠와나
, "221" // 세네갈
, "248" // 세이셸
, "249" // 수단
, "268" // 스와질란드

, "232" // 시에라리온
, "244" // 앙골라
, "251" // 에티오피아
, "256" // 우간다
, "20" // 이집트
, "260" // 잠비아

, "263" // 짐바브웨
, "237" // 카메룬
, "254" // 케냐
, "242" // 콩고
, "255" // 탄자니아
, "228" // 토고

, "216" // 튀니지
, "977" // 네팔
, "64" // 뉴질랜드
, "886" // 대만
, "82" // 대한민국
, "1809" // 도미니카 공화국

, "856" // 라오스
, "596" // 마르티니크
, "853" // 마카오
, "60" // 말레이시아
, "960" // 몰디브
, "976" // 몽고

, "95" // 미얀마
, "678" // 바누아투
, "880" // 방글라데시
, "84" // 베트남
, "975" // 부탄
, "673" // 브루나이

, "1670" // 사이판
, "94" // 스리랑카
, "65" // 싱가포르
, "93" // 아프가니스탄
, "91" // 인도
, "62" // 인도네시아

, "81" // 일본
, "86" // 중국
, "855" // 캄보디아
, "269" // 코모로
, "66" // 태국
, "993" // 투르크메니스탄

, "92" // 파키스탄
, "679" // 피지
, "63" // 필리핀
, "852" // 홍콩
, "1473" // 그레나다
, "52" // 멕시코

, "1246" // 바베이도스
, "1441" // 버뮤다
, "58" // 베네수엘라
, "501" // 벨리즈
, "591" // 볼리비아
, "55" // 브라질

, "1758" // 세인트루시아
, "1784" // 세인트빈센트 그레나딘
, "297" // 아루바
, "54" // 아르헨티나
, "1268" // 앤티가 바부다
, "503" // 엘살바도르

, "1876" // 자메이카
, "56" // 칠레
, "1345" // 케이맨 제도 
, "57" // 콜롬비아
, "53" // 쿠바
, "1868" // 트리니다드토바고

, "507" // 파나마
, "595" // 파라과이
, "51" // 페루
, "1" // 푸에르토리코
, "995" // 그루지아
, "30" // 그리스

, "299" // 그린랜드
, "31" // 네덜란드
, "599" // 네덜란드령 안틸레스
, "47" // 노르웨이
, "45" // 덴마크
, "49" // 독일

, "371" // 라트비아
, "7" // 러시아
, "262" // 레위니옹
, "40" // 루마니아
, "352" // 룩셈부르크
, "218" // 리비아

, "370" // 리투아니아
, "423" // 리히텐슈타인
, "389" // 마케도니아 공화국
, "377" // 모나코2
, "373" // 몰도바
, "32" // 벨기에

, "375" // 벨라루스
, "387" // 보스니아 헤르체고비나
, "257" // 부룬디
, "226" // 부르키나파소
, "359" // 불가리아
, "39" // 산마리노

, "381" // 세르비아 몬테그로
, "46" // 스웨덴
, "41" // 스위스
, "34" // 스페인
, "421" // 슬로바키아
, "386" // 슬로베니아

, "374" // 아르메니아
, "354" // 아이슬란드
, "353" // 아일랜드
, "376" // 안도라
, "355" // 알바니아
, "213" // 알제리

, "372" // 에스토니아
, "44" // 영국
, "43" // 오스트리아
, "380" // 우크라이나
, "39" // 이탈리아
, "350" // 지브롤터

, "420" // 체코
, "385" // 크로아티아
, "90" // 터키
, "298" // 페로 제도
, "351" // 포르투갈
, "48" // 폴란드

, "33" // 프랑스
, "594" // 프랑스령기아나 
, "689" // 프랑스령폴리네시아
, "358" // 핀란드
, "36" // 헝가리
, "961" // 레바논

, "973" // 바레인
, "966" // 사우디아라비아
, "963" // 시리아
, "971" // 아랍에미리트연합국
, "994" // 아제르바이잔
, "967" // 예맨

, "968" // 오만
, "962" // 요르단
, "964" // 이라크
, "98" // 이란
, "972" // 이스라엘
, "377" // 코소보

, "965" // 쿠웨이트
, "970" // 팔레스타인
, "1" // 미국
, "509" // 아이티
, "1" // 캐나다
, "590" // 과들루프

, "502" // 과테말라
, "1671" // 괌
, "245" // 기니
, "687" // 뉴칼레도니
, "505" // 니카라과
, "691" // 마이크로네시아

, "356" // 말타
, "229" // 베닌
, "597" // 수리남
, "225" // 아이보리코스트
, "593" // 에콰도르
, "1809" // 영국령 버진 제도

, "61" // 오스트레일리아
, "504" // 온두라스
, "598" // 우루과이
, "998" // 우즈베키스탄
, "236" // 중앙아프리카공화국
, "253" // 지부티

, "235" // 차드
, "374" // 나고르노카라바흐
, "238" // 카보베르데
, "7" // 카자흐스탄
, "1345" // 케이맨 제도
, "506" // 코스타리카

, "243" // 콩고민주공화국
, "974" // 카타르
, "996" // 키르기즈스탄
, "686" // 키리바시
, "992" // 타지키스탄
, "676" // 통가

, "993" // 투르크메니스탄
, "670" // 모르
, "689" // 프랑스령 폴리네시아
];

function setNationNum() { 
    var nationObj = document.getElementById("nation");

    var nationObjValue = nationObj.options[ nationObj.selectedIndex].value;

    if ( nationObjValue == '' ) { 
        document.getElementById("country_code").value = '';
        return; 
    }
    var tmpStrArr = nationObjValue.split('^^'); 

    document.getElementById("country_code").value = tmpStrArr[0];
}


function setContinent() {
    var selectObj 		= document.getElementById("nation");
    var selectObj2 	= document.getElementById("nation2");
    var continentObj 	= document.getElementById("continent");

    subList2( continentObj, selectObj2, selectObj );

    setNationNum();
}

function initlist2( subM, subS ) {
    subS.length = subM.length	 ;

    for ( i = 0; i < subM.length; i++ ) {
        m = subM.options[i].value.indexOf('@@')

        if ( m != -1 || ( m == -1 &&  subM.value == '' ) ) {
            subS.options[i].value	= subM.options[i].value.substring( m + 2 );
            subS.options[i].continent = subM.options[i].value.substring(0, m);
            subS.options[i].text		= subM.options[i].text;
            var tmpStrArr = subS.options[i].value.split('^^'); 
            subS.options[i].code = tmpStrArr[0];
        } else {
            subS.options[i].value	= " ";
            subS.options[i].text	= " ";
       }
    }
}

function subList2 ( main, sub, view ) {
    initlist2( sub, view );
    len 	= sub.length-1;

    val 	= main.value; 

    if ( val != " " && val != "" ) {

        for ( k = len ; k >= 0 ; k-- ) {
            n = sub.options[k].value.indexOf('@@')

            if ( (sub.options[k].value).substring( 0, n ) != val && n != -1 ) {
                view.options[k] = null;
            }
        }
    }

    view.selectedIndex = 0;
}


// 국제문자이면 국가번호를 구하고 전화번호를 분리시킨다
function splitFullNumber(phoneNum) {
    var countryCode;
    if (phoneNum.charAt(0) == '+') {
        for (i = 1; i < 5; i++) {
            if ((idx = jQuery.inArray(phoneNum.substr(1, i), country_codes)) > -1) {
                countryCode = country_codes[idx];
                phoneNum = '0' + phoneNum.substr(i+1);
                break;
            }
        }
    }
    return [countryCode, phoneNum];
}


function makeFullNumber(country, phoneNum) {
    var fullNum = phoneNum;
    if (country.length > 0) {
        if (fullNum.charAt(0) == '0') fullNum = fullNum.substr(1);
        fullNum = '+' + country + fullNum;
    } else return phoneNum;
    return fullNum;
}


(function($) {
    jQuery(function($) {
        setContinent();
    });
}) (jQuery);
