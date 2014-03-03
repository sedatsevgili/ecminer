<?php
require_once("../utils/config.php");

require_once("../lib/core/CategoricalAttribute.php");
require_once("../lib/core/DataSet.php");
require_once("../lib/classifier/DecisionTree.php");

$weatherAttribute = new CategoricalAttribute("Weather");
$parentsAttribute = new CategoricalAttribute("Parents");
$moneyAttribute = new CategoricalAttribute("Money");

$class = new SampleClass(Attribute::ATTRIBUTE_TYPE_CATEGORICAL,"Weekend");
$attributeArray = array($weatherAttribute,$parentsAttribute,$moneyAttribute);

$sample1 = new Sample($attributeArray,$class);
$sample1->fillWithAttributeValues("Sunny,Yes,Rich");
$sample1->setClassValue("Cinema");

$sample2 = new Sample($attributeArray,$class);
$sample2->fillWithAttributeValues("Sunny,No,Rich");
$sample2->setClassValue("Tennis");

$sample3 = new Sample($attributeArray,$class);
$sample3->fillWithAttributeValues("Windy,Yes,Rich");
$sample3->setClassValue("Cinema");

$sample4 = new Sample($attributeArray,$class);
$sample4->fillWithAttributeValues("Rainy,Yes,Poor");
$sample4->setClassValue("Cinema");

$sample5 = new Sample($attributeArray,$class);
$sample5->fillWithAttributeValues("Rainy,No,Rich");
$sample5->setClassValue("Stay in");

$sample6 = new Sample($attributeArray,$class);
$sample6->fillWithAttributeValues("Rainy,Yes,Poor");
$sample6->setClassValue("Cinema");

$sample7 = new Sample($attributeArray,$class);
$sample7->fillWithAttributeValues("Windy,No,Poor");
$sample7->setClassValue("Cinema");

$sample8 = new Sample($attributeArray,$class);
$sample8->fillWithAttributeValues("Windy,No,Rich");
$sample8->setClassValue("Shopping");

$sample9 = new Sample($attributeArray,$class);
$sample9->fillWithAttributeValues("Windy,Yes,Rich");
$sample9->setClassValue("Cinema");

$sample10 = new Sample($attributeArray,$class);
$sample10->fillWithAttributeValues("Sunny,No,Rich");
$sample10->setClassValue("Tennis");


$dataSet = new DataSet($attributeArray,$class,
array($sample1,$sample2,$sample3,$sample4,$sample5,$sample6,$sample7,$sample8,$sample9,$sample10)
);
$decisionTree = new DecisionTree($dataSet);

$newSample = new Sample($attributeArray,$class);
$newSample->fillWithAttributeValues("Rainy,No,Poor");
$estimatedClassValue = $decisionTree->getClassOfSample($newSample);
var_dump($estimatedClassValue);

/*require_once("../lib/classifier/NaiveBayes.php");
require_once("../lib/core/CategoricalAttribute.php");
require_once("../lib/core/NumericalAttribute.php");

$ageAttribute = new NumericalAttribute("Age","");
$carAttribute = new CategoricalAttribute("Car","");
$class = new SampleClass(Attribute::ATTRIBUTE_TYPE_CATEGORICAL,"");
$attributeArray = array($ageAttribute,$carAttribute);

$sample1 = new Sample($attributeArray,$class);
$sample1->fillWithAttributeValues("25,sports");
$sample1->setClassValue("L");

$sample2 = new Sample($attributeArray,$class);
$sample2->fillWithAttributeValues("20,vintage");
$sample2->setClassValue("H");

$sample3 = new Sample($attributeArray,$class);
$sample3->fillWithAttributeValues("25,sports");
$sample3->setClassValue("L");

$sample4 = new Sample($attributeArray,$class);
$sample4->fillWithAttributeValues("45,suv");
$sample4->setClassValue("H");

$sample5 = new Sample($attributeArray,$class);
$sample5->fillWithAttributeValues("20,sports");
$sample5->setClassValue("H");

$sample6 = new Sample($attributeArray,$class);
$sample6->fillWithAttributeValues("25,sup");
$sample6->setClassValue("H");

$dataSet = new DataSet($attributeArray,$class,array($sample1,$sample2,$sample3,$sample4,$sample5,$sample6));
$naiveBayesClassifier = new NaiveBayes($dataSet);

$newSample = new Sample($attributeArray,$class);
$newSample->fillWithAttributeValues("23,truck");

$estimation = $naiveBayesClassifier->estimateClassOfSample($newSample,true);
var_dump($estimation);*/


/*$colorAttribute = new CategoricalAttribute("Color","");
$class = new SampleClass(Attribute::ATTRIBUTE_TYPE_CATEGORICAL,"Class","");
$attributeArray = array($colorAttribute);

$sample1 = new Sample($attributeArray,$class);
$sample1->fillWithAttributeValues("R");
$sample1->setClassValue("H");

$sample2 = new Sample($attributeArray,$class);
$sample2->fillWithAttributeValues("G");
$sample2->setClassValue("L");

$sample3 = new Sample($attributeArray,$class);
$sample3->fillWithAttributeValues("R");
$sample3->setClassValue("H");

$sample4 = new Sample($attributeArray,$class);
$sample4->fillWithAttributeValues("Y");
$sample4->setClassValue("L");

$sample5 = new Sample($attributeArray,$class);
$sample5->fillWithAttributeValues("R");
$sample5->setClassValue("L");

$sample6 = new Sample($attributeArray,$class);
$sample6->fillWithAttributeValues("G");
$sample6->setClassValue("H");

$dataSet = new DataSet($attributeArray,$class,array($sample1,$sample2,$sample3,$sample4,$sample5,$sample6));
$naiveBayesClassifier = new NaiveBayes($dataSet,NaiveBayes::$TYPE_CATEGORICAL);

$newSample = new Sample($attributeArray,$class);
$newSample->fillWithAttributeValues("Y");

$estimation = $naiveBayesClassifier->estimateClassOfSample($newSample);
var_dump($estimation);*/

/*$mpAttribute = new CategoricalAttribute("Magazine Promotion","");
$wpAttribute = new CategoricalAttribute("Watch Promotion","");
$lipAttribute = new CategoricalAttribute("Life Insurance Promotion");
$cciAttribute = new CategoricalAttribute("Credit Card Insurance","");
$sex = new SampleClass(Attribute::ATTRIBUTE_TYPE_CATEGORICAL,"Sex","");

$attributeArray = array($mpAttribute,$wpAttribute,$lipAttribute,$cciAttribute);

$sample1 = new Sample($attributeArray,$sex);
$sample1->fillWithAttributeValues("Yes,No,No,No",",");
$sample1->setClassValue("Male");

$sample2 = new Sample($attributeArray,$sex);
$sample2->fillWithAttributeValues("Yes,Yes,Yes,Yes",",");
$sample2->setClassValue("Female");

$sample3 = new Sample($attributeArray,$sex);
$sample3->fillWithAttributeValues("No,No,No,No",",");
$sample3->setClassValue("Male");

$sample4 = new Sample($attributeArray,$sex);
$sample4->fillWithAttributeValues("Yes,Yes,Yes,Yes",",");
$sample4->setClassValue("Male");

$sample5 = new Sample($attributeArray,$sex);
$sample5->fillWithAttributeValues("Yes,No,Yes,No",",");
$sample5->setClassValue("Female");

$sample6 = new Sample($attributeArray,$sex);
$sample6->fillWithAttributeValues("No,No,No,No",",");
$sample6->setClassValue("Female");

$sample7 = new Sample($attributeArray,$sex);
$sample7->fillWithAttributeValues("Yes,Yes,Yes,Yes",",");
$sample7->setClassValue("Male");

$sample8 = new Sample($attributeArray,$sex);
$sample8->fillWithAttributeValues("No,No,No,No",",");
$sample8->setClassValue("Male");

$sample9 = new Sample($attributeArray,$sex);
$sample9->fillWithAttributeValues("Yes,No,No,No",",");
$sample9->setClassValue("Male");

$sample10 = new Sample($attributeArray,$sex);
$sample10->fillWithAttributeValues("Yes,Yes,Yes,No",",");
$sample10->setClassValue("Female");

$dataSet = new DataSet($attributeArray,$sex,array($sample1,$sample2,$sample3,$sample4,$sample5,$sample6,$sample7,$sample8,$sample9,$sample10));

$naiveBayesClassifier = new NaiveBayes($dataSet,NaiveBayes::$TYPE_CATEGORICAL);

$newSample = new Sample($attributeArray,$sex);
$newSample->fillWithAttributeValues("Yes,Yes,No,No",",");

$estimation = $naiveBayesClassifier->estimateClassOfSample($newSample);
var_dump($estimation);*/
