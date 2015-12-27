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

import React from 'react';
import HelloWorld from '../components/HelloWorld';
import NotFoundPage from '../components/app/NotFoundPage';
import { Route, NotFoundRoute } from 'react-router';

export default (
  <Route>
    <Route handler={HelloWorld} path="/"/>

    <NotFoundRoute handler={NotFoundPage}/>
  </Route>
);
