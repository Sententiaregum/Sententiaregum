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

import Translate from 'react-translate-component';
import React from 'react';
import Grid from 'react-bootstrap/lib/Grid';
import Row from 'react-bootstrap/lib/Row';
import Col from 'react-bootstrap/lib/Col';
import InfoBox from './login/InfoBox';
import Form from './login/Form';
import Panel from 'react-bootstrap/lib/Panel';

/**
 * Login component.
 *
 * @returns {React.Element} The landing page markup.
 */
const Login = () => {
  return (
    <div>
      <h1><Translate content="pages.portal.login.headline" /></h1>
      <Grid className="container-without-padding">
        <Row className="inversed-column-container">
          <Col md={6} className="grid-item-1">
            <Panel bsStyle="success" header={<Translate content="pages.portal.login.panels.login" />}>
              <Form />
            </Panel>
          </Col>
          <Col md={6} className="grid-item-2">
            <Panel bsStyle="info" header={<Translate content="pages.portal.login.panels.info" />}>
              <InfoBox />
            </Panel>
          </Col>
        </Row>
      </Grid>
    </div>
  );
};

export default Login;
