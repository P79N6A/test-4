'use strict';

module.exports = function(grunt) {

  grunt.initConfig({
    apidoc: {
        yyb: {
          src: "app/",
          dest: "apidoc/"
        }
    }
  });

  // load plugins tasks
  //grunt.loadTasks('tasks');

  // Tasks
  //grunt.loadNpmTasks('grunt-contrib-jshint');
  //grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-apidoc');

  // Tasks: Default
  //grunt.registerTask('default', ['jshint']);

  // Tasks: Test
  //grunt.registerTask('test', ['clean', 'apidoc']);

};
