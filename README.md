#QSPS

Quite Simple Publishing System

This is a single-author content management system. It's as simple as it can be.

###Contents: 
1.    Motivation  
2.    Features  
3.    Future Improvements  

##1. Motivation:

##2. Features:

* Plain-file index (no database)
* Single-user
* Fire-And-Forget

##3. Future Improvements

* Content Upload protocol  
    Right now QSPS relies on the user to manually upload a file and populate the ``__index__`` file. An improvement
    would be to use an authenticated protocol to do this job for us.
* Multi-user support  
    QSPS lacks multi-user support. That's because in my blog I'm the only one who's publishing any content, therefore
    I had no need to support multiple authors. This may not suit other users.
* Content access protection  
    At the moment all content is public. Introduce a groups-based (or other) policy to allow priviledged publishing. 
* Multiple text marking schemes
* Comments support
