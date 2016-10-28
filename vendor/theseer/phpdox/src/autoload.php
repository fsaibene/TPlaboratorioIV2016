<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
spl_autoload_register(
    function($class) {
        static $classes = null;
        if ($classes === null) {
            $classes = array(
                'theseer\\phpdox\\application' => '/Application.php',
                'theseer\\phpdox\\applicationexception' => '/Application.php',
                'theseer\\phpdox\\backendbootstrapapi' => '/bootstrap/BackendBootstrapApi.php',
                'theseer\\phpdox\\bootstrap' => '/bootstrap/Bootstrap.php',
                'theseer\\phpdox\\bootstrapapi' => '/bootstrap/BootstrapApi.php',
                'theseer\\phpdox\\bootstrapexception' => '/bootstrap/Bootstrap.php',
                'theseer\\phpdox\\buildconfig' => '/config/BuildConfig.php',
                'theseer\\phpdox\\cli' => '/CLI.php',
                'theseer\\phpdox\\clienvironmentexception' => '/CLI.php',
                'theseer\\phpdox\\clioptions' => '/CLIOptions.php',
                'theseer\\phpdox\\clioptionsexception' => '/CLIOptions.php',
                'theseer\\phpdox\\collector\\abstractunitobject' => '/collector/project/AbstractUnitObject.php',
                'theseer\\phpdox\\collector\\abstractvariableobject' => '/collector/project/AbstractVariableObject.php',
                'theseer\\phpdox\\collector\\backend\\backendinterface' => '/collector/backend/BackendInterface.php',
                'theseer\\phpdox\\collector\\backend\\factory' => '/collector/backend/Factory.php',
                'theseer\\phpdox\\collector\\backend\\factoryexception' => '/collector/backend/Factory.php',
                'theseer\\phpdox\\collector\\backend\\originalvaluelexer' => '/collector/backend/parser/OriginalValueLexer.php',
                'theseer\\phpdox\\collector\\backend\\parseerrorexception' => '/collector/backend/ParseErrorException.php',
                'theseer\\phpdox\\collector\\backend\\parseresult' => '/collector/backend/ParseResult.php',
                'theseer\\phpdox\\collector\\backend\\phpparser' => '/collector/backend/parser/PHPParser.php',
                'theseer\\phpdox\\collector\\backend\\sourcefileexception' => '/collector/backend/SourceFileException.php',
                'theseer\\phpdox\\collector\\backend\\unitcollectingvisitor' => '/collector/backend/parser/UnitCollectingVisitor.php',
                'theseer\\phpdox\\collector\\classobject' => '/collector/project/ClassObject.php',
                'theseer\\phpdox\\collector\\collector' => '/collector/Collector.php',
                'theseer\\phpdox\\collector\\collectorexception' => '/collector/CollectorException.php',
                'theseer\\phpdox\\collector\\constantobject' => '/collector/project/ConstantObject.php',
                'theseer\\phpdox\\collector\\dependency' => '/collector/project/Dependency.php',
                'theseer\\phpdox\\collector\\domcollectioninterface' => '/collector/project/DOMCollectionInterface.php',
                'theseer\\phpdox\\collector\\indexcollection' => '/collector/project/IndexCollection.php',
                'theseer\\phpdox\\collector\\indexcollectionexception' => '/collector/project/IndexCollection.php',
                'theseer\\phpdox\\collector\\inheritanceresolver' => '/collector/InheritanceResolver.php',
                'theseer\\phpdox\\collector\\inlinecomment' => '/collector/project/InlineComment.php',
                'theseer\\phpdox\\collector\\interfaceobject' => '/collector/project/InterfaceObject.php',
                'theseer\\phpdox\\collector\\memberobject' => '/collector/project/MemberObject.php',
                'theseer\\phpdox\\collector\\methodobject' => '/collector/project/MethodObject.php',
                'theseer\\phpdox\\collector\\methodobjectexception' => '/collector/project/MethodObject.php',
                'theseer\\phpdox\\collector\\parameterobject' => '/collector/project/ParameterObject.php',
                'theseer\\phpdox\\collector\\pathconverteriterator' => '/collector/PathConverterIterator.php',
                'theseer\\phpdox\\collector\\project' => '/collector/project/Project.php',
                'theseer\\phpdox\\collector\\projectexception' => '/collector/project/Project.php',
                'theseer\\phpdox\\collector\\sourcecollection' => '/collector/project/SourceCollection.php',
                'theseer\\phpdox\\collector\\sourcefile' => '/collector/project/SourceFile.php',
                'theseer\\phpdox\\collector\\traitobject' => '/collector/project/TraitObject.php',
                'theseer\\phpdox\\collector\\unitobjectexception' => '/collector/project/AbstractUnitObject.php',
                'theseer\\phpdox\\collectorconfig' => '/config/CollectorConfig.php',
                'theseer\\phpdox\\collectorinterface' => '/collector/CollectorInterface.php',
                'theseer\\phpdox\\configexception' => '/config/GlobalConfig.php',
                'theseer\\phpdox\\configloader' => '/config/ConfigLoader.php',
                'theseer\\phpdox\\configloaderexception' => '/config/ConfigLoader.php',
                'theseer\\phpdox\\docblock\\descriptionparser' => '/docblock/parser/DescriptionParser.php',
                'theseer\\phpdox\\docblock\\docblock' => '/docblock/DocBlock.php',
                'theseer\\phpdox\\docblock\\docblockexception' => '/docblock/DocBlock.php',
                'theseer\\phpdox\\docblock\\factory' => '/docblock/Factory.php',
                'theseer\\phpdox\\docblock\\factoryexception' => '/docblock/Factory.php',
                'theseer\\phpdox\\docblock\\genericelement' => '/docblock/GenericElement.php',
                'theseer\\phpdox\\docblock\\genericelementexception' => '/docblock/GenericElement.php',
                'theseer\\phpdox\\docblock\\genericparser' => '/docblock/parser/GenericParser.php',
                'theseer\\phpdox\\docblock\\inlineprocessor' => '/docblock/InlineProcessor.php',
                'theseer\\phpdox\\docblock\\internalparser' => '/docblock/parser/InternalParser.php',
                'theseer\\phpdox\\docblock\\invalidelement' => '/docblock/InvalidElement.php',
                'theseer\\phpdox\\docblock\\invalidparser' => '/docblock/parser/InvalidParser.php',
                'theseer\\phpdox\\docblock\\licenseparser' => '/docblock/parser/LicenseParser.php',
                'theseer\\phpdox\\docblock\\paramparser' => '/docblock/parser/ParamParser.php',
                'theseer\\phpdox\\docblock\\parser' => '/docblock/Parser.php',
                'theseer\\phpdox\\docblock\\varelement' => '/docblock/VarElement.php',
                'theseer\\phpdox\\docblock\\varparser' => '/docblock/parser/VarParser.php',
                'theseer\\phpdox\\enginebootstrapapi' => '/bootstrap/EngineBootstrapApi.php',
                'theseer\\phpdox\\enrichconfig' => '/config/EnrichConfig.php',
                'theseer\\phpdox\\enricherbootstrapapi' => '/bootstrap/EnricherBootstrapApi.php',
                'theseer\\phpdox\\errorexception' => '/shared/ErrorException.php',
                'theseer\\phpdox\\errorhandler' => '/shared/ErrorHandler.php',
                'theseer\\phpdox\\factory' => '/shared/Factory.php',
                'theseer\\phpdox\\factoryexception' => '/shared/Factory.php',
                'theseer\\phpdox\\factoryinterface' => '/shared/FactoryInterface.php',
                'theseer\\phpdox\\fileinfo' => '/shared/FileInfo.php',
                'theseer\\phpdox\\fileinfoexception' => '/shared/FileInfo.php',
                'theseer\\phpdox\\generator\\abstractclassevent' => '/generator/events/AbstractClassEvent.php',
                'theseer\\phpdox\\generator\\abstractcollection' => '/generator/project/collections/AbstractCollection.php',
                'theseer\\phpdox\\generator\\abstractentry' => '/generator/project/entries/AbstractEntry.php',
                'theseer\\phpdox\\generator\\abstractevent' => '/generator/AbstractEvent.php',
                'theseer\\phpdox\\generator\\abstractunitobject' => '/generator/project/objects/AbstractUnitObject.php',
                'theseer\\phpdox\\generator\\classcollection' => '/generator/project/collections/ClassCollection.php',
                'theseer\\phpdox\\generator\\classconstantevent' => '/generator/events/ClassConstantEvent.php',
                'theseer\\phpdox\\generator\\classendevent' => '/generator/events/ClassEndEvent.php',
                'theseer\\phpdox\\generator\\classentry' => '/generator/project/entries/ClassEntry.php',
                'theseer\\phpdox\\generator\\classmemberevent' => '/generator/events/ClassMemberEvent.php',
                'theseer\\phpdox\\generator\\classmethodevent' => '/generator/events/ClassMethodEvent.php',
                'theseer\\phpdox\\generator\\classobject' => '/generator/project/objects/ClassObject.php',
                'theseer\\phpdox\\generator\\classstartevent' => '/generator/events/ClassStartEvent.php',
                'theseer\\phpdox\\generator\\constantcollection' => '/generator/project/collections/ConstantCollection.php',
                'theseer\\phpdox\\generator\\constantevent' => '/generator/events/ConstantEvent.php',
                'theseer\\phpdox\\generator\\constantobject' => '/generator/project/objects/ConstantObject.php',
                'theseer\\phpdox\\generator\\engine\\abstractengine' => '/generator/engine/AbstractEngine.php',
                'theseer\\phpdox\\generator\\engine\\engineinterface' => '/generator/engine/EngineInterface.php',
                'theseer\\phpdox\\generator\\engine\\eventhandlerregistry' => '/generator/engine/EventHandlerRegistry.php',
                'theseer\\phpdox\\generator\\engine\\eventhandlerregistryexception' => '/generator/engine/EventHandlerRegistry.php',
                'theseer\\phpdox\\generator\\engine\\factory' => '/generator/engine/Factory.php',
                'theseer\\phpdox\\generator\\engine\\factoryexception' => '/generator/engine/Factory.php',
                'theseer\\phpdox\\generator\\engine\\html' => '/generator/engine/html/Html.php',
                'theseer\\phpdox\\generator\\engine\\html\\functions' => '/generator/engine/html/functions.php',
                'theseer\\phpdox\\generator\\engine\\htmlconfig' => '/generator/engine/html/HtmlConfig.php',
                'theseer\\phpdox\\generator\\engine\\xml' => '/generator/engine/xml/Xml.php',
                'theseer\\phpdox\\generator\\enricher\\abstractenricher' => '/generator/enricher/AbstractEnricher.php',
                'theseer\\phpdox\\generator\\enricher\\build' => '/generator/enricher/build/Build.php',
                'theseer\\phpdox\\generator\\enricher\\checkstyle' => '/generator/enricher/checkstyle/CheckStyle.php',
                'theseer\\phpdox\\generator\\enricher\\checkstyleconfig' => '/generator/enricher/checkstyle/CheckStyleConfig.php',
                'theseer\\phpdox\\generator\\enricher\\classenricherinterface' => '/generator/enricher/ClassEnricherInterface.php',
                'theseer\\phpdox\\generator\\enricher\\endenricherinterface' => '/generator/enricher/EndEnricherInterface.php',
                'theseer\\phpdox\\generator\\enricher\\enricherexception' => '/generator/enricher/EnricherException.php',
                'theseer\\phpdox\\generator\\enricher\\enricherinterface' => '/generator/enricher/EnricherInterface.php',
                'theseer\\phpdox\\generator\\enricher\\factory' => '/generator/enricher/Factory.php',
                'theseer\\phpdox\\generator\\enricher\\factoryexception' => '/generator/enricher/Factory.php',
                'theseer\\phpdox\\generator\\enricher\\fullenricherinterface' => '/generator/enricher/FullEnricherInterface.php',
                'theseer\\phpdox\\generator\\enricher\\git' => '/generator/enricher/git/Git.php',
                'theseer\\phpdox\\generator\\enricher\\gitconfig' => '/generator/enricher/git/GitConfig.php',
                'theseer\\phpdox\\generator\\enricher\\gitenricherexception' => '/generator/enricher/git/Git.php',
                'theseer\\phpdox\\generator\\enricher\\interfaceenricherinterface' => '/generator/enricher/InterfaceEnricherInterface.php',
                'theseer\\phpdox\\generator\\enricher\\phploc' => '/generator/enricher/phploc/PHPLoc.php',
                'theseer\\phpdox\\generator\\enricher\\phplocconfig' => '/generator/enricher/phploc/PHPLocConfig.php',
                'theseer\\phpdox\\generator\\enricher\\phpmessdetector' => '/generator/enricher/pmd/PHPMessDetector.php',
                'theseer\\phpdox\\generator\\enricher\\phpmessdetectorconfig' => '/generator/enricher/pmd/PHPMessDetectorConfig.php',
                'theseer\\phpdox\\generator\\enricher\\phpunit' => '/generator/enricher/phpunit/PHPUnit.php',
                'theseer\\phpdox\\generator\\enricher\\phpunitconfig' => '/generator/enricher/phpunit/PHPUnitConfig.php',
                'theseer\\phpdox\\generator\\enricher\\startenricherinterface' => '/generator/enricher/StartEnricherInterface.php',
                'theseer\\phpdox\\generator\\enricher\\traitenricherinterface' => '/generator/enricher/TraitEnricherInterface.php',
                'theseer\\phpdox\\generator\\generator' => '/generator/Generator.php',
                'theseer\\phpdox\\generator\\generatorexception' => '/generator/Generator.php',
                'theseer\\phpdox\\generator\\index' => '/generator/project/Index.php',
                'theseer\\phpdox\\generator\\inlinecommentcollection' => '/generator/project/collections/InlineCommentCollection.php',
                'theseer\\phpdox\\generator\\inlinecommentobject' => '/generator/project/objects/InlineCommentObject.php',
                'theseer\\phpdox\\generator\\interfacecollection' => '/generator/project/collections/InterfaceCollection.php',
                'theseer\\phpdox\\generator\\interfaceconstantevent' => '/generator/events/InterfaceConstantEvent.php',
                'theseer\\phpdox\\generator\\interfaceendevent' => '/generator/events/InterfaceEndEvent.php',
                'theseer\\phpdox\\generator\\interfaceentry' => '/generator/project/entries/InterfaceEntry.php',
                'theseer\\phpdox\\generator\\interfacemethodevent' => '/generator/events/InterfaceMethodEvent.php',
                'theseer\\phpdox\\generator\\interfaceobject' => '/generator/project/objects/InterfaceObject.php',
                'theseer\\phpdox\\generator\\interfacestartevent' => '/generator/events/InterfaceStartEvent.php',
                'theseer\\phpdox\\generator\\membercollection' => '/generator/project/collections/MemberCollection.php',
                'theseer\\phpdox\\generator\\memberevent' => '/generator/events/MemberEvent.php',
                'theseer\\phpdox\\generator\\memberobject' => '/generator/project/objects/MemberObject.php',
                'theseer\\phpdox\\generator\\methodcollection' => '/generator/project/collections/MethodCollection.php',
                'theseer\\phpdox\\generator\\methodevent' => '/generator/events/MethodEvent.php',
                'theseer\\phpdox\\generator\\methodobject' => '/generator/project/objects/MethodObject.php',
                'theseer\\phpdox\\generator\\namespaceclassesendevent' => '/generator/events/NamespaceClassesEndEvent.php',
                'theseer\\phpdox\\generator\\namespaceclassesstartevent' => '/generator/events/NamespaceClassesStartEvent.php',
                'theseer\\phpdox\\generator\\namespacecollection' => '/generator/project/collections/NamespaceCollection.php',
                'theseer\\phpdox\\generator\\namespaceendevent' => '/generator/events/NamespaceEndEvent.php',
                'theseer\\phpdox\\generator\\namespaceentry' => '/generator/project/entries/NamespaceEntry.php',
                'theseer\\phpdox\\generator\\namespaceinterfacesendevent' => '/generator/events/NamespaceInterfacesEndEvent.php',
                'theseer\\phpdox\\generator\\namespaceinterfacesstartevent' => '/generator/events/NamespaceInterfacesStartEvent.php',
                'theseer\\phpdox\\generator\\namespacestartevent' => '/generator/events/NamespaceStartEvent.php',
                'theseer\\phpdox\\generator\\namespacetraitsendevent' => '/generator/events/NamespaceTraitsEndEvent.php',
                'theseer\\phpdox\\generator\\namespacetraitsstartevent' => '/generator/events/NamespaceTraitsStartEvent.php',
                'theseer\\phpdox\\generator\\phpdoxclassesendevent' => '/generator/events/PHPDoxClassesEndEvent.php',
                'theseer\\phpdox\\generator\\phpdoxclassesstartevent' => '/generator/events/PHPDoxClassesStartEvent.php',
                'theseer\\phpdox\\generator\\phpdoxendevent' => '/generator/events/PHPDoxEndEvent.php',
                'theseer\\phpdox\\generator\\phpdoxinterfacesendevent' => '/generator/events/PHPDoxInterfacesEndEvent.php',
                'theseer\\phpdox\\generator\\phpdoxinterfacesstartevent' => '/generator/events/PHPDoxInterfacesStartEvent.php',
                'theseer\\phpdox\\generator\\phpdoxnamespacesendevent' => '/generator/events/PHPDoxNamespacesEndEvent.php',
                'theseer\\phpdox\\generator\\phpdoxnamespacesstartevent' => '/generator/events/PHPDoxNamespacesStartEvent.php',
                'theseer\\phpdox\\generator\\phpdoxrawevent' => '/generator/events/PHPDoxRawEvent.php',
                'theseer\\phpdox\\generator\\phpdoxstartevent' => '/generator/events/PHPDoxStartEvent.php',
                'theseer\\phpdox\\generator\\phpdoxtraitsendevent' => '/generator/events/PHPDoxTraitsEndEvent.php',
                'theseer\\phpdox\\generator\\phpdoxtraitsstartevent' => '/generator/events/PHPDoxTraitsStartEvent.php',
                'theseer\\phpdox\\generator\\project' => '/generator/project/Project.php',
                'theseer\\phpdox\\generator\\projectexception' => '/generator/project/Project.php',
                'theseer\\phpdox\\generator\\service' => '/generator/Service.php',
                'theseer\\phpdox\\generator\\sourcetree' => '/generator/project/SourceTree.php',
                'theseer\\phpdox\\generator\\traitcollection' => '/generator/project/collections/TraitCollection.php',
                'theseer\\phpdox\\generator\\traitconstantevent' => '/generator/events/TraitConstantEvent.php',
                'theseer\\phpdox\\generator\\traitendevent' => '/generator/events/TraitEndEvent.php',
                'theseer\\phpdox\\generator\\traitentry' => '/generator/project/entries/TraitEntry.php',
                'theseer\\phpdox\\generator\\traitmemberevent' => '/generator/events/TraitMemberEvent.php',
                'theseer\\phpdox\\generator\\traitmethodevent' => '/generator/events/TraitMethodEvent.php',
                'theseer\\phpdox\\generator\\traitobject' => '/generator/project/objects/TraitObject.php',
                'theseer\\phpdox\\generator\\traitstartevent' => '/generator/events/TraitStartEvent.php',
                'theseer\\phpdox\\generatorconfig' => '/config/GeneratorConfig.php',
                'theseer\\phpdox\\generatorconfigexception' => '/config/GeneratorConfig.php',
                'theseer\\phpdox\\globalconfig' => '/config/GlobalConfig.php',
                'theseer\\phpdox\\hasfileinfoexception' => '/shared/HasFileInfoException.php',
                'theseer\\phpdox\\inheritanceconfig' => '/config/InheritanceConfig.php',
                'theseer\\phpdox\\parserbootstrapapi' => '/bootstrap/ParserBootstrapApi.php',
                'theseer\\phpdox\\progresslogger' => '/logger/ProgressLogger.php',
                'theseer\\phpdox\\progressloggerexception' => '/logger/ProgressLogger.php',
                'theseer\\phpdox\\projectconfig' => '/config/ProjectConfig.php',
                'theseer\\phpdox\\shellprogresslogger' => '/logger/ShellProgressLogger.php',
                'theseer\\phpdox\\version' => '/shared/Version.php'
            );
        }
        $cn = strtolower($class);
        if (isset($classes[$cn])) {
            require __DIR__ . $classes[$cn];
        }
    }
);
// @codeCoverageIgnoreEnd
