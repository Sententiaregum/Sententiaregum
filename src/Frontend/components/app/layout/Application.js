/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React      from 'react';
import AppMenu    from './AppMenu';

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
      <AppMenu />
      {props.children}
    </div>
  );
};

Application.propTypes = {
  children: React.PropTypes.node
};

export default Application;
