function convert_time_to_words(time_in_seconds,long = false){  
    time_in_seconds = parseInt(time_in_seconds);
    hours = Math.floor(time_in_seconds / 3600);
    minutes = Math.floor((time_in_seconds - (hours * 3600)) / 60);          

    timeString = "";
    
    if(hours >= 24){
        var days = parseInt(hours / 24);
        var hours = parseInt(hours % 24);

        if(days > 1){
            timeString += days+" days ";              
        }
        else{
            timeString += days+" day ";
        }            

        if(long == true){
            if(hours > 0){
            timeString += set_hours(hours);
            }
            if(minutes > 0){
            timeString += set_minutes(minutes);
            }              
        }
        else{
            if(hours > 0){
            timeString += set_hours(set_hour_interval(hours));
            }
        }
    }
    else{
        if(hours >= 1){              
            timeString = set_hours(hours);                
            
            if(long == true){                
            if(minutes > 0){
                timeString += set_minutes(minutes);
            }
            }
            else{                
            if(minutes > 0 ){                
                set_minutes(set_minute_interval(minutes));
            }
            }
        }
        else{
            if(minutes > 0 ){                
            set_minutes(minutes);
            }              
        }           
    }

    if(time_in_seconds == 0){
      timeString = "";
    }
    
    return timeString;
  }

function set_hours(hours){
    timeString = "";
    if(hours == 1){
        timeString += hours+" hr "
    }
    else{
        timeString += hours+" hrs ";
    }
    return timeString;
}

function set_minutes(minutes){
    timeString = "";
    if(minutes > 1){
        timeString += minutes+" mins "
    }
    else{
        timeString += minutes+" min "
    }
    return timeString;
}

function set_minute_interval(minutes){          
    if(minutes >= 15 && minutes < 30){
        return 15;
    }
    else if(minutes >= 30 && minutes < 45){
        return 30;
    }
    else if(minutes >= 30 && minutes < 60){
        return 45;
    }
    else{
        return 0;
    }
}

function set_hour_interval(hours){          
    if(hours >= 2 && hours < 4){
        return 2;
    }
    else if(hours >= 4 && hours < 6){
        return 4;
    }
    else if(hours >= 6 && hours < 8){
        return 6;
    }
    else if(hours >= 8 && hours < 10){
        return 8;
    }
    else if(hours >= 10 && hours < 12){
        return 10;
    }
    else if(hours >= 12 && hours < 14){
        return 12;
    }
    else if(hours >= 14 && hours < 16){
        return 14;
    }
    else if(hours >= 16 && hours < 18){
        return 16;
    }
    else if(hours >= 18 && hours < 20){
        return 18;
    }
    else if(hours >= 20 && hours < 22){
        return 20;
    }
    else if(hours >= 22 && hours < 24){
        return 22;
    }
}