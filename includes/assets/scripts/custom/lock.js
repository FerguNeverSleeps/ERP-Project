var Lock = function () {

    return {
        //main function to initiate the module
        init: function () {

             $.backstretch([
		        "../includes/assets/img/bg/1.jpg",
		        //"../includes/assets/img/bg/2.jpg",
		        "../includes/assets/img/bg/3.jpg",
		        "../includes/assets/img/bg/4.jpg"
		        ], {
		          fade: 1000,
		          duration: 2000
		      });
        }

    };

}();