/*
    * Utinfra Utils
    * Ajax 및 필요한 Javascript Util 취급

 */

const baseUrl = "http://110.10.147.8/medicalitem/";
/*
    @param cookieKey :String
    @param cookieVal :String
    @param exDays    :Number
 */
let setCookie = (cookieKey, cookieVal, exDays) => {
    let exdate = new Date();
    exdate.setDate(exdate.getDate() + exDays);

    let cookie_val = `${escape(cookieVal)}; expires=${exdate.toString()}`;

    if (document.cookie.length !== 0)
        document.cookie = "";

    document.cookie = `${cookieKey}=${cookie_val}`;
}

/*
    @param cookieKey  :String
    @returns String?
 */

let getCookie = (cookieKey) => {
    let cookieTot = document.cookie.split(";")

    for (let i = 0; i < cookieTot.length; i++) {
        let x = cookieTot[i].substr(0, cookieTot[i].indexOf("="))
        let y = cookieTot[i].substr(cookieTot[i].indexOf("=") + 1)

        if (x == cookieKey) {
            return unescape(y)
        }
    }
    return null
}

/*
    비동기 Ajax 통신
    @param url
    @param paramData
    @param callback
    @param method
 */
//url a링크 대신 들어올 것 나머지는 파라미터
let callAjax = (url, paramData, callback, method = "POST") => {
    let contType = 'application/json'
    let process = true

    if (paramData instanceof FormData) {
        contType = false;
        process = false;
    }
    // JSON String이 아닌경우
      if (typeof(paramData) !== "string") {
          contType = false
          process  = false
      }


    let request = $.ajax({ //ajax함수 사용해서 ajax요청 생성
        url: baseUrl.concat(url) ,
        method: method,
        data: paramData,
        processData : process,
        contentType: contType,
    });

    request.done((data) => { // 요청이 성공했을때 불러올 콜백함수 서버에서 받은 응답데이터를 data에 담음
        callback(data)
    });

    request.fail((xhr) => { //요청이 실패했을때 호출할 콜백함수 등록/ 콘솔에 출력하고 경고장 표시
        console.log(`Ajax Failed ${xhr.responseText}`)
        return alert("오류가 발생하였습니다.")
    });
}

let getAddressUrl = (url, arrMap = null) => {
    let totalUrl = baseUrl + url
    if (arrMap != null) {
        let firstParam = true
        for (let [key,value] of Object.entries(arrMap)) {
            if (firstParam)
                totalUrl += `?${key}=${value}`
            else
                totalUrl += `&${key}=${value}`
            firstParam = false
        }
    }
    return totalUrl;
}

let openWindow = (url, arrMap = null, strStyle = null) => {
    let totalUrl = getAddressUrl(url, arrMap);
    let matWin = window.open(totalUrl, "matListPopUp", strStyle);
    return matWin;
}



// 소숫점 자리표시
let trans_number = (number_string) => {
    let number = parseFloat(number_string);
    return number.toFixed(1);
}

let trans_ratio = (total_number,ratio_string) => {
    let ratio_number = parseFloat(ratio_string);

    if(ratio_number <= 0)
    {
        return 0;
    }
    let ratio = (ratio_number / total_number *100).toFixed(1);

    console.log(ratio);

    return ratio;
}


