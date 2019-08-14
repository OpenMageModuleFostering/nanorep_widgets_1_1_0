// if (annyang) {
  	// // Let's define a command
  	var nanorep_questions = [{label:"כמה זה עולה?",data:0,objectId:"27923761",count:0,percent:0,likes:0},{label:"How to locate billing address",data:0,objectId:"19047959",count:0,percent:0,likes:0},{label:"What is the Tag Module Hierarchy?",data:0,objectId:"22779720",count:0,percent:0,likes:0},{label:"How does the Data Replication operate?",data:0,objectId:"22779702",count:0,percent:0,likes:0},{label:"How does the Distributed Data Storage work?",data:0,objectId:"22779678",count:0,percent:0,likes:0},{label:"What is the Client/Server Architecture?",data:0,objectId:"22779636",count:0,percent:0,likes:0},{label:"where can I find pricing information?",data:0,objectId:"19050627",count:0,percent:0,likes:0},{label:"How do I get a premium verification letter?",data:0,objectId:"19048186",count:0,percent:0,likes:0},{label:"How do I send out a provider directory?",data:0,objectId:"19048171",count:0,percent:0,likes:0},{label:"Reinstatement due to joining another plan",data:0,objectId:"19048140",count:0,percent:0,likes:0},{label:"How do I send out the rating for a plan?",data:0,objectId:"19048089",count:0,percent:0,likes:0},{label:"How do I change payment method?",data:0,objectId:"19048079",count:0,percent:0,likes:0},{label:"explaining billing with no consent",data:0,objectId:"19048014",count:0,percent:0,likes:0},{label:"How do you find the Market the member's plan is sold?",data:0,objectId:"19047899",count:0,percent:0,likes:0},{label:"How do you change a member's demographics?",data:0,objectId:"19047831",count:0,percent:0,likes:0},{label:"What if someone calls to inform CompanyX that member is deceased?",data:0,objectId:"19047803",count:0,percent:0,likes:0}];
  	// var commands = {};
// 	  
 	// for (q = 0; q < nanorep_questions.length; q++){
 		// if(nanorep_questions[q] != undefined){
	 		// console.log(nanorep_questions[q].label);
	 		// var c = { "question" : q, "nanorep_questions" : nanorep_questions };
	  		// commands[nanorep_questions[q].label] = function() { console.log(this); }.bind(c);
	  	// }
  	// }
//   	
	// annyang.debug();
// 	
	// annyang.addCommands(commands);
	// console.log(commands);
  	// // Start listening.
  	// annyang.start();
// }

(function () {
  /*global annyang,jQuery */
  "use strict";
  var root = this;

  if (annyang) {
    annyang.debug();
    annyang.setLanguage('he');
    
    var askFullQuestion = function(pre_question ,question) {
    	console.log(question);
    	switch(pre_question){
    		case "האם":
    		case "מה":
    		case "למה":
    		case "כמה":
    		case "איפה":
    		case "מתי":
      			$('search').setValue(pre_question + ' ' + question);
      		break;
     	}
    };
    
    var askQuestion = function(question) {
    	console.log(question);
		$('search').setValue(question);

    };
  	
    annyang.addCommands({
    	'יש לי שאלה *question' : askQuestion, 
    	'שאלה *question' : askQuestion,
    	'ברצוני לדעת *question' : askQuestion,
    	'ברצוני לשאול *question' : askQuestion,
    	':pre_question *question' : askFullQuestion
      	// 'I like to ask a question *etc' : askQuestion,
      	// 'I\'d to ask a question *etc' : askQuestion,
    });  
    annyang.start();
  }

}).call(this);