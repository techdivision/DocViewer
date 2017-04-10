# TechDivision.DocViewer

This Neos package provides a backend module which renders Markdown in the "Documentation" folder of all installed 
packages. The idea is Neos.Editors are able to read a project specific manual about the project.

For examle in your Neos site are some complex editing or configuration features for the Neos.Editors. With this module 
are you able to provide them a manuel for your project.

## Configuration

There are some options in the Settings.yaml to disable rendering of defined packages.

## Known issue

Currently the resources controller which provides files of the Documentation folder ignores the Policy.yaml. 


## TBD

- secure the resource provider by security framework
- secure the parsing and resource provider only for visible packages
- configurable package which gets rendered by default
- configurable optional sub-directory inside the Documentation directory for rendering for enable the possibility for a
developer documentation and an user manual
- branched version for LTS and current Neos version