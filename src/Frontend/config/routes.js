/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React                                         from 'react';
import Application                                   from '../components/app/layout/Application';
import { IndexRoute, Router, Route, hashHistory }    from 'react-router';
import { syncHistoryWithStore }                      from 'react-router-redux';
import { Provider }                                  from 'react-redux';
import store                                         from './redux/store';
import { protectApp, guardFromPortal }               from '../util/security/appProfileChecker';
import NotFoundPage                                  from '../components/app/layout/NotFoundPage';

const HelloWorld = () => {
  return (
    <h1>Hello World!</h1>
  )
};

const Protected = () => {
  return <h1>Secret page!</h1>;
};

//TODO: Change to browesrHistory
const history = syncHistoryWithStore(hashHistory, store);

//TODO: Re-add other components after redux has been implemented there too
export default (
  <Provider store={store}>
    <Router history={history}>
      <Route component={Application} path="/">
        <IndexRoute component={HelloWorld} onEnter={guardFromPortal} />
        <Route component={Protected} onEnter={protectApp} path="secret"  />
        <Route component={NotFoundPage} path="*" />
      </Route>
    </Router>
  </Provider>
);
