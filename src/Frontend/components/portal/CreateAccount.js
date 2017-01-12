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

import React     from 'react';
import Translate from 'react-translate-component';
import Form      from './signup/Form';
import InfoBox   from './signup/InfoBox';

/**
 * Markup component which renders the `create account` page.
 *
 * @returns {React.Element} The markup.
 */
export default () => {
  return (
    <div>
      <h1><Translate content="pages.portal.head" /></h1>
      <div>
        <InfoBox />
        <Form />
      </div>
    </div>
  );
};
