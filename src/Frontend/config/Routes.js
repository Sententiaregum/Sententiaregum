/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

/**
 * @author Benjamin Bieler <benjaminbieler2014@gmail.com>
 */
import React from 'react';
import helloWorld from '../components/HelloWorld';
import testComponent from '../components/TestComponent';
import menu from '../components/app/Menu';
import Router from 'react-router';

var DefaultRoute = Router.DefaultRoute;
var Route        = Router.Route;

export default (
    <Route name="app" path="/" handler={testComponent}></Route>
);
