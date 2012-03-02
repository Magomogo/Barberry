# The problem

Every web project have different binary data to host and handle: images, PDFs, etc. In most cases
those files should be converted in different ways:

* images: resize and format
* PDF to image
* Open office templates to MS Office documents and PDFs

# Solution

This application can host a binary/text data (**Documents**) of a particular content type and
convert it in requested way.

It intended to be deployed at separate hostname as bin._project-hostname_

# Usage

We desided to use REST interface. Here are the commands.

## POST

Submitting data to the service.

    Url: /
    Data: Document and (optionally) variables to parse if Document is a template
    Return: {id : "_auto_generated_id_string_"}


## GET

Getting a **document**.

    Url: /_id_string_._ext_
    Data: no
    Return: Document

**Example**: /12wkr234ser349.pdf - get document "12wkr234ser349" as PDF

## DELETE

Deleting a **document**.

    Url: /_id_string_
    Data: no
    Return: {}

# Specials

Some documents shouldn't be available to everyone. Access rules to be implemented (apache basic auth?).

