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

import React      from 'react';
import AppMenu    from './AppMenu';
import {Provider} from 'react-redux';
import store    from '../../../config/store';

/**
 * Container for the whole app.
 *
 * @param {Object} props The properties of the app container.
 *
 * @returns {React.Element} The markup of the app.
 */
const Application = props => {
  return (
    <div className="container">
      <Provider store={store}>
        <AppMenu />
      </Provider>
      {props.children}
    </div>
  );
};

Application.propTypes = {
  children: React.PropTypes.node
};

export default Application;
