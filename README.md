# Blog Module 4.0

The Blog module allows authorized users to maintain a blog. Blogs are a series of posts that are time stamped and are typically viewed by date as you would view a journal. Blog entries can be made public or private to the site members, depending on which roles have access to view content.

##Post API Services

##List post

###all
route

``https://mydomain.com/api/iblog/posts?``

filters
```json
filter={"categories":{1,2,3},"editor":[1,3,2], }

```
