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
import Router from 'react-router';
import routes from './config/routes';

Router.run(routes, function (Root) {
    let page = (
        <Root />
    );

    React.render(page, document.getElementById('content'));
});