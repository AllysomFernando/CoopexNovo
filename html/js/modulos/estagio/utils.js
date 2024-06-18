/**
 * 
 * @param {string} startTime - tempo inicial no formato "HH:mm"
 * @param {string} endTime - tempo final no formato "HH:mm"
 * @returns intervalo de tempo entre os horários fornecidos no formato HH:mm
 */
function calculaDiferencaDeTempo(startTime, endTime) {
  var start = moment(startTime, "HH:mm");
    var end = moment(endTime, "HH:mm");
    var minutes = end.diff(start, 'minutes');
    var divisao = minutes / 60;
    minutes = 60 * divisao;
    
    var interval = moment().hour(0).minute(minutes);

    // interval.subtract(.15, 'minutes');
    // console.log(interval)
    return interval.format("HH:mm");
}

/**
 * 
 * @param {string} time - tempo inicial no formato "HH:mm"
 * @returns horário formatado no padrão da biblioteca Moment no formato "HH:mm"
 */
function formataHorarioParaHorasEMinutos(time) {
    var start = moment(time, "HH:mm");
    var minutes = start;
    var divisao = minutes / 60;
    minutes = 60 * divisao;
    
    var interval = moment().hour(0).minute(minutes);

    // interval.subtract(.15, 'minutes');
    // console.log(interval)
    return interval.format("HH:mm");
}
